<?php

namespace TravisSouth\Gitup;

class Config implements ConfigInterface
{

    private $endpoint;

    private $credentials;
    /**
     * Sets the configuration needed if needed.
     *
     * @param array $credentials
     *
     * @return void
     */
    public function createConfig(array $config)
    {
        $this->credentials = $config['private_token'] ??
            throw new InvalidArgumentException('private_token key does not exist');
        $this->endpoint = $config['endpoint'] ??
            throw new InvalidArgumentException('endpoint key does not exist');
    }

    /**
     * Retrieves credentials for your configuration
     *
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Retrieves API endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
