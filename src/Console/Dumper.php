<?php

namespace TravisSouth\Gitup\Console;

use Symfony\Component\Console\Output\OutputInterface;

class Dumper implements DumperInterface
{

    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function debug($anything)
    {
        $this->output($anything, OutputInterface::VERBOSITY_DEBUG);
    }

    public function info($anything)
    {
        $this->output($anything, OutputInterface::VERBOSITY_NORMAL);
    }

    public function error($anything)
    {
        $this->output($anything, OutputInterface::VERBOSITY_NORMAL);
    }

    public function notice($anything)
    {
        $this->output($anything, OutputInterface::VERBOSITY_NORMAL);
    }

    private function output($anything, $level)
    {
        if ($this->output->getVerbosity() >= $level) {
            dump($anything);
        }
    }
}
