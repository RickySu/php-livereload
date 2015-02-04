<?php
namespace PHPLivereload\Response;

use PHPLivereload\Protocol\WebSocket\Frame;

/**
 * Description of Request
 *
 * @author ricky
 */
class ResponseWebSocketFrame
{
    protected $frame;

    public function __construct(Frame $frame)
    {
        $this->frame = $frame;
    }

    public function __toString()
    {
        return $this->frame->encode();
    }
}
