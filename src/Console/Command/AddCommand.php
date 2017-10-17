<?php

namespace TravisSouth\Gitup\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use TravisSouth\Gitup\Adapters\Gitlab;
use TravisSouth\Gitup\Config;
use TravisSouth\Gitup\Console\Dumper;
use Psr\Log\LogLevel;

class AddCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('Adds configurations and/or variables based on input')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('provider', 'p', InputOption::VALUE_OPTIONAL),
                ))
            )
            ->setHelp('This command allows you to add configurations and/or variables based on input');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbosityLevelMap = array(
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
        );
        $logger = new ConsoleLogger($output, $verbosityLevelMap);
        $dumper = new Dumper($output);
        $config = new Config();
        switch ($input->getOption('provider')) {
            default:
                $git = new Gitlab($logger, $dumper);
                $config->createConfig([
                    'private_token' => 'ojhsbHaXSExqBnTyk-uS',
                    'endpoint' => 'https://gitlab.ph.esl-asia.com/api/v4/projects/276/variables/',
                ]);
                $git->prepareConfig($config);
                $git->addConfig([
                    'test' => 'kaboom',
                    'kaboom' => 'test',
                ]);
                break;
        }
    }
}
