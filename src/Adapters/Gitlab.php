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

    private $results = [];

    private $logger;

    private $dumper;

    private $queryString = null;

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
                    $this->logger->debug($response->getBody());
                    $response = json_decode($response->getBody());
                    if ($response) {
                        $this->results[$index] = $response;
                        $this->dumper->info($response);
                    }
                },
                'rejected' => function ($reason, $index) {
                    $errorBody = $reason->getResponseBodySummary($reason->getResponse());
                    $this->logger->error(sprintf('Encountered errors on index %s', $index));
                    $this->logger->error(sprintf('Error message: %s', $reason->getMessage()));
                    $this->logger->error(sprintf('Error code: %s', $reason->getCode()));
                    $this->logger->error(sprintf('Error body: %s', $errorBody));
                    $this->results[$index] = $errorBody;
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
        $updatedPath = '';
        if($this->queryString)
            $updatedPath = '?' . http_build_query($this->queryString);
        foreach ($lists as $listKey => $listValue) {
            yield function () use ($method, $listValue, $listKey, $updatedPath) {
                $path = '';
                switch (strtoupper($method)) {
                    case 'GET':
                    case 'DELETE':
                        $path = $listValue . $updatedPath;
                        $data = [];
                        break;
                    case 'POST':
                        $data = [
                            'key' => $listKey,
                            'value' => $listValue
                        ];
                        $path = $updatedPath;
                        break;
                    case 'PUT':
                        $data = [
                            'value' => $listValue
                        ];
                        $path = $listKey . $updatedPath;
                        break;
                    default:
                        $data = [];
                        $path = $updatedPath;
                }
                $this->logger->debug(sprintf('Endpoint = %s', $this->endpoint . $path));
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
     * Retrieves all config to your git server.
     *
     * @return void
     */
    public function getAllConfig()
    {
        $this->logger->info('Getting all configurations...');
        $lists = [ "blank" => "" ];
        $this->queryString = [ 'per_page' => 200 ];
        $this->prepareRequest($lists);
        $this->dumper->debug($this->results);
        $this->formatAllOutput();
    }

    private function formatAllOutput()
    {
        $convertedArray = [];
        $tabbedOutput = '';
        foreach ($this->results[0] as $result) {
            $this->dumper->debug('inside results');
            $this->dumper->debug($result);
            $convertedArray[$result->key] = $result->value;
            $tabbedOutput .= $result->key . "\t" . $result->value . "\n";
        }
        $this->dumper->debug($convertedArray);
        $keyValueJson = json_encode($convertedArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        $this->dumper->info($tabbedOutput);
        $this->dumper->info($keyValueJson);
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
