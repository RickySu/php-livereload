<?php
namespace PHPLivereload\Protocol;

use React\Socket\Connection as SocketConnection;
use PHPLivereload\Response\ResponseWebSocketFrame;
use PHPLivereload\Application\ServerApplication;

class LivereloadProtocol
{
    protected $conn;
    protected $app;
    protected $connected = false;

    public function __construct(SocketConnection $conn, ServerApplication $app)
    {
        $this->conn = $conn;
        $this->app = $app;
        $this->app->addClient($this);
        $this->initEvent();
    }

    public function reload($file, $config)
    {
        echo "reload $file\n";
        $this->sendCommand(array(
            'command' => 'reload',
            'path' => $file,
            'liveCSS' => $config['liveCSS'],
        ));
    }

    protected function shutdown()
    {
        $this->app->removeClient($this);
        unset($this->app);
        unset($this->conn);
    }

    protected function initEvent()
    {
        $this->conn->on('command', function($command){
            $this->dispatchCommand($command);
        });
        $this->conn->on('close', function(){
            $this->shutdown();
        });
    }

    protected function sendRaw($data)
    {
        $response = new ResponseWebSocketFrame(WebSocket\Frame::generate($data));
        $this->conn->write($response);
    }

    protected function sendCommand($command)
    {
        $this->sendRaw(json_encode($command));
    }

    protected function dispatchCommand($command)
    {
        switch($command['command']){
            case 'hello':
                $this->processCommandHello($command);
                break;
            default:
                //$this->conn->end();
        }
    }

    protected function processCommandHello($command)
    {
        if($this->connected){
           return;
        }
        $this->connected = true;
        $this->sendCommand(array(
            'command' => 'hello',
            'protocols' => array(
                'http://livereload.com/protocols/official-7',
            ),
            'serverName' => 'php-livereload',
        ));
    }
}