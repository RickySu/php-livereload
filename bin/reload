#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;


$finder = new Finder();
$finder->files()->in(__DIR__.'/../src/Command/');

$application = new Application();

foreach ($finder as $file) {
    $class = 'PHPLivereload\\Command\\'.substr($file->getFileName(), 0, - (strlen($file->getExtension())+1));
    $application->add(new $class);
}

$application->run();