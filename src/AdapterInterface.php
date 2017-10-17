<?php

namespace TravisSouth\Gitup;

use Psr\Log\LoggerInterface;
use TravisSouth\Gitup\ConfigInterface;

interface AdapterInterface
{
    /**
     * Sets the configurations (credentials, URL endpoint, etc.) for your git integration server.
     *
     * @param ConfigInterface $config
     *
     * @return void
     */
    public function prepareConfig(ConfigInterface $config);

    /**
     * Add configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function addConfig(array $list);

    /**
     * Update configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function updateConfig(array $list);

    /**
     * Remove config to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function removeConfig(array $list);

    /**
     * Retrieves config to your git server.
     *
     * @param mixed $list
     *
     * @return void
     */
    public function getConfig($list);
}
