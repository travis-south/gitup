<?php

namespace TravisSouth\Gitup;

class GitConfig implements GitConfigInterface
{
    public function __construct(AdapterInterface $adapter, ConfigInterface $config)
    {
        $this->adapter = $adapter;
        $this->setConfig($config);
    }

    /**
     * Sets the configurations (credentials, URL endpoint, etc.) for your git integration server.
     *
     * @param ConfigInterface $config
     *
     * @return void
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->adapter->prepareConfig($config);
    }

    /**
     * Add (or update if existing) configs to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function addGitConfig($list)
    {
        $this->adapter->addConfig($list);
    }

    /**
     * Remove config to your git server.
     *
     * @param array $list
     *
     * @return void
     */
    public function removeGitConfig($list)
    {
        $this->adapter->removeConfig($list);
    }
}
