<?php
namespace PHPLivereload\Protocol;

use PHPLivereload\Application\ServerApplication;
use React\Socket\Connection as SocketConnection;
use Symfony\Component\HttpFoundation;
use PHPLivereload\Response\Response;

class WebSocketProtocol
{
    protected $websocket;
    protected $conn;

    public function __construct(HttpFoundation\Request $request, SocketConnection $conn, ServerApplication $app)
    {
        $this->conn = $conn;
        $this->initEvent();
        $this->handshake($request);
        new LivereloadProtocol($conn, $app);
    }

    protected function handshake(HttpFoundation\Request $request)
    {
        if (!($handshakeResponse = $this->websocket->handshake($request))) {
            $this->conn->write(new Response('bad protocol', 400), true);
            return;
        }
        $this->conn->write($handshakeResponse);
    }

    protected function initEvent()
    {
        $this->websocket = new WebSocket\WebSocket();
        $this->conn->on('data', function($data){
            $this->onData($data);
        });
    }

    protected function onData($data)
    {
        $frame = $this->websocket->onMessage($data);
        if(!($frame instanceof WebSocket\Frame)) {
            return;
        }
        if(($command = json_decode($frame->getData(), true)) === null){
            return;
        }
        $this->conn->emit('command', array($command));
    }
}
