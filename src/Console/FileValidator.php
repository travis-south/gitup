<?php

namespace TravisSouth\Gitup\Console;

class FileValidator
{

    private $file;

    private $fullPath;

    private $data;

    public function __construct($file = '')
    {
        $this->file = $file ?? 'git-config.json';
    }

    public function validate()
    {
        $this->checkFile();
        $this->checkConfig();
        return $this->data;
    }

    private function checkFile()
    {
        $this->fullPath = getcwd() . DIRECTORY_SEPARATOR . $this->file;
        if(!file_exists($this->fullPath)){
            throw new \Exception(sprintf('File %s does not exists.', $this->fullPath));
        }
    }

    private function checkConfig()
    {
        $str = file_get_contents($this->fullPath);
        $this->data = json_decode($str, true);
        if(!isset($this->data['config']))
            throw new \DomainException(sprintf('config key does not exist.'));
        if(!isset($this->data['config']['credentials']))
            throw new \DomainException(sprintf('credentials key does not exist.'));
        if(!isset($this->data['config']['endpoint']))
            throw new \DomainException(sprintf('endpoint key does not exist.'));
        if(!isset($this->data['data']))
            throw new \DomainException(sprintf('data key does not exist.'));
    }
}
