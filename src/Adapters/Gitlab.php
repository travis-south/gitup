<?php

namespace TravisSouth\Gitup;

use TravisSouth\Gitup\AdapterInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\RequestInterface;

class Gitlab implements AdapterInterface
{

    const TIMEOUT = 2;

    private $endpoint;

    private $requests = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
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
        $this->prepareRequest($lists);
        $response = $this->client->sendAsync($this->request, ['timeout' => self::TIMEOUT]);
    }

    private function prepareRequest($lists)
    {
        foreach ($lists as $list) {
            $clonedRequest = $this->request->withMethod('POST');
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
    }
}
