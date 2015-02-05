<?php
namespace PHPLivereload\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('livereload:init')
            ->setDescription('Initialize livereload.json.')
            ->addOption('force', '-f', InputOption::VALUE_NONE, 'force rewrite livereload.json')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $forceRewrite = $input->getOption('force');
        if(!$forceRewrite && file_exists('livereload.json')){
            $output->writeln("<error>livereload.json file exists.\nplease use --force to overwrite.</error>");
            return;
        }
        $this->writeConfig($input, $output);
    }

    protected function writeConfig(InputInterface $input, OutputInterface $output)
    {
        $json = <<<EOT
{
    "period": 1,
    "watch": {
        "web/css/": "*.css",
        "web/js/": "*.js",
        "web/img/": "\\\\.png|gif|jpg$"
    }
}
EOT
;
        file_put_contents('livereload.json', $json);
        $output->writeln("<info>livereload.json is generated.</info>");
    }
}