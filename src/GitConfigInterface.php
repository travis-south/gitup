<?php

namespace TravisSouth\Gitup;

interface GitConfigInterface
{
    /**
     * Sets the configurations (credentials, URL endpoint, etc.) for your git integration server.
     *
     * @param ConfigInterface $config
     *
     * @return void
     */
    private function setConfig(ConfigInterface $config);

    /**
     * Add (or update if existing) configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function addGitConfig($list);

    /**
     * Remove config to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function removeGitConfig($list);
}
