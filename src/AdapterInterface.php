<?php

namespace TravisSouth\Gitup;

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
     * Add (or update if existing) configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function addConfig(array $list);

    /**
     * Remove config to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function removeConfig(array $list);
}
