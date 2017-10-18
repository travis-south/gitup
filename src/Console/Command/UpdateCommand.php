<?php

namespace TravisSouth\Gitup\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Updates configurations and/or variables based on input')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('provider', 'p', InputOption::VALUE_OPTIONAL),
                ))
            )
            ->addArgument('config_file', InputArgument::OPTIONAL, 'Path of your data file.')
            ->setHelp('This command allows you to update configurations and/or variables based on input');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareCommand($input, $output, 'updateConfig');
    }
}
