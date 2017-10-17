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
use TravisSouth\Gitup\Console\FileValidator;

class RemoveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('remove')
            ->setDescription('Remove value of vars')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('provider', 'p', InputOption::VALUE_OPTIONAL),
                ))
            )
            ->addArgument('config_file', InputArgument::OPTIONAL, 'Path of your data file.')
            ->setHelp('This command allows you to retrieve configurations and/or variables based on input');
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
        
        $configFile = $input->getArgument('config_file');
        $validator = new FileValidator($configFile);
        $data = $validator->validate();
        $dumper->debug($data);

        switch ($input->getOption('provider')) {
            default:
                $git = new Gitlab($logger, $dumper);
                $config->createConfig([
                    'private_token' => $data['config']['credentials'],
                    'endpoint' => $data['config']['endpoint'],
                ]);
                $git->prepareConfig($config);
                $git->removeConfig($data['data']);
                break;
        }
    }
}
