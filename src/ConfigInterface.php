<?php

namespace TravisSouth\Gitup;

interface ConfigInterface
{
    /**
     * Sets the configuration needed if needed.
     *
     * @param array $credentials
     *
     * @return void
     */
    public function createConfig(array $config);

    /**
     * Retrieves credentials for your configuration
     *
     * @return array
     */
    public function getCredentials();

    /**
     * Retrieves API endpoint.
     *
     * @return string
     */
    public function getEndpoint();
}
