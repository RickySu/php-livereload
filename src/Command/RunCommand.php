<?php
namespace PHPLivereload\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPLivereload\Application\ServerApplication;

class RunCommand extends Command
{
    protected function configure()
    {
        $this->setName('livereload:run')
            ->setDescription('Starts a live reload server.')
            ->addArgument('address', InputArgument::OPTIONAL, 'Address:port', '127.0.0.1:35729')
            ->addOption('config', '-c', InputOption::VALUE_OPTIONAL, 'Path to livereload.json')
            ->addOption('no-watch', '', InputOption::VALUE_NONE, 'Disable watching')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $noWatching = $input->getOption('no-watch');
        $address = $input->getArgument('address', '127.0.0.1:35729');
        list($host, $port) = explode(':', $address);
        $output->writeln(sprintf("Server running on <info>http://%s</info>\n", $input->getArgument('address')));
        $output->writeln('Quit the server with CONTROL-C.');
        $app = new ServerApplication($host, $port);
        if(!$noWatching){
            $config = $this->loadConfig($input, $output);
            $app->watching($config['period'], $config);
        }
        $app->run();
    }

    protected function loadConfig(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config');
        if($configFile === null){
            $configFile = 'livereload.json';
        }
        if(!file_exists($configFile)){
            throw new \Exception("$configFile not found.");
        }

        $config = json_decode(file_get_contents($configFile), true);
        return $config;
    }
}