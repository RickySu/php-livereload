<?php
namespace PHPLivereload\Application;

use Symfony\Component\Console\Output\OutputInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as SocketServer;
use PHPLivereload\Protocol;
use Symfony\Component\Finder\Finder;

class ServerApplication
{
    protected $output;
    protected $loop;
    protected $clients = array();
    protected $config = array(
        'liveCSS' => true,
    );
    protected $watchConfig;
    protected $watchingFiles = array();

    public function __construct($host = '127.0.0.1', $port = 35729)
    {
        $this->initLoop();
        $this->initServer($host, $port);
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function run()
    {
        $this->loop->run();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function watching($time, $config)
    {
        $this->watchConfig = $config;
        $this->scanFiles();
        $this->loop->addPeriodicTimer($time, function(){
            $this->watchingFileChange();
        });
    }

    protected function scanFiles($scanNewFile = true)
    {
        foreach($this->watchConfig['watch'] as $path => $file){
            $finder = new Finder();
            try{
                foreach($finder->in($path)->name($file)->followLinks() as $file){
                    if($file->getRealPath() && !isset($this->watchingFiles[$file->getRealpath()])){
                        $this->watchingFiles[$file->getRealpath()] = $scanNewFile?$file->getMTime():0;
                    }
                }
            }
            catch(\InvalidArgumentException $e){
                continue;
            }
        }
    }

    protected function watchingFileChange()
    {
        $this->scanFiles(false);
        foreach($this->watchingFiles as $file => $time){
            $mtime = @filemtime($file);
            if($mtime && $mtime > $time){
                $this->watchingFiles[$file] = $mtime;
                $this->reloadFile($file);
            }
        }
    }

    protected function initLoop()
    {
        $this->loop = LoopFactory::create();
    }

    protected function initServer($host, $port)
    {
        $socket = new SocketServer($this->loop);
        $socket->listen($port, $host);
        return new Protocol\HttpProtocol($socket, $this);
    }

    public function addClient(Protocol\LivereloadProtocol $client)
    {
        if(!in_array($client, $this->clients)){
            $this->clients[] = $client;
        }
    }

    public function removeClient(Protocol\LivereloadProtocol $client)
    {
        $index = array_search($client, $this->clients, true);
        if($index == false){
            return;
        }
        unset($this->clients[$index]);
    }

    public function reloadFile($file)
    {
        foreach($this->clients as $client){
            $client->reload($file, $this->config);
        }
    }
}