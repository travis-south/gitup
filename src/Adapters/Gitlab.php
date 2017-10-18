<?php

namespace TravisSouth\Gitup\Adapters;

use TravisSouth\Gitup\AdapterInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;
use TravisSouth\Gitup\ConfigInterface;
use TravisSouth\Gitup\Console\DumperInterface;
use GuzzleHttp\Promise;

class Gitlab implements AdapterInterface
{

    const TIMEOUT = 2;

    const CONCURRENCY = 50;

    private $endpoint;

    private $requests = [];

    private $logger;

    private $dumper;

    public function __construct(LoggerInterface $logger, DumperInterface $dumper)
    {
        $this->logger = $logger;
        $this->dumper = $dumper;
    }

    /**
     * Sets the configurations (credentials, URL endpoint, etc.) for your git integration server.
     *
     * @param ConfigInterface $config
     *
     * @return void
     */
    public function prepareConfig(ConfigInterface $config)
    {
        $this->endpoint = $config->getEndpoint();
        $this->private_token = $config->getCredentials();
    }

    /**
     * Add (or update if existing) configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function addConfig(array $lists)
    {
        $this->logger->info('Adding configurations...');
        $this->prepareRequest($lists, 'POST');
    }

    private function prepareRequest($lists, $method = 'GET')
    {
        try {
            $this->client = new Client([
                'base_uri' => $this->endpoint,
                'verify' => false
            ]);
            $this->logger->debug('Starting requests...');
            $this->logger->debug(sprintf('Method = %s', $method));

            $pool = new Pool($this->client, $this->createRequests($lists, $method), [
                'concurrency' => self::CONCURRENCY,
                'fulfilled' => function ($response, $index) {
                    $this->logger->info(sprintf('Successfully executed index %s', $index));
                    $response = json_decode($response->getBody());
                    if($response)
                        $this->dumper->info($response);
                },
                'rejected' => function ($reason, $index) {
                    $errorBody = $reason->getResponseBodySummary($reason->getResponse());
                    $this->logger->error(sprintf('Encountered errors on index %s', $index));
                    $this->logger->error(sprintf('Error message: %s', $reason->getMessage()));
                    $this->logger->error(sprintf('Error code: %s', $reason->getCode()));
                    $this->logger->error(sprintf('Error body: %s', $errorBody));
                },
            ]);

            $promise = $pool->promise();
            $promise->wait();
        } catch (\Exception $e) {
            $this->logger->error($e);
            return [];
        }
    }

    private function createRequests($lists, $method)
    {
        foreach ($lists as $listKey => $listValue) {
            yield function () use ($method, $listValue, $listKey) {
                $path = '';
                switch (strtoupper($method)) {
                    case 'GET':
                    case 'DELETE':
                        $path = $listValue;
                        $data = [];
                        break;
                    case 'POST':
                        $data = [
                            'key' => $listKey,
                            'value' => $listValue
                        ];
                        $path = '';
                        break;
                    case 'PUT':
                        $data = [
                            'value' => $listValue
                        ];
                        $path = $listKey;
                        break;
                    default:
                        $data = [];
                        $path = '';
                }
                $this->logger->debug(sprintf('Endpoint = %s', $this->endpoint));
                $this->logger->debug(sprintf('Key = %s', $listKey));
                $this->logger->debug(sprintf('Value = %s', $listValue));
                return $this->client->requestAsync(strtoupper($method), $path, [
                    'headers' => [
                        'PRIVATE-TOKEN' => $this->private_token,
                    ],
                    'form_params' => $data,
                ]);
            };
        }
    }

    /**
     * Remove config to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function removeConfig(array $lists)
    {
        $this->logger->info('Removing configurations...');
        $this->prepareRequest($lists, 'DELETE');
    }

    /**
     * Retrieves config to your git server.
     *
     * @param mixed $lists
     *
     * @return void
     */
    public function getConfig($lists = [])
    {
        $this->logger->info('Getting configurations...');
        $this->prepareRequest($lists);
    }

    /**
     * Update configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function updateConfig(array $lists)
    {
        $this->logger->info('Updating configurations...');
        $this->prepareRequest($lists, 'PUT');
    }
}
