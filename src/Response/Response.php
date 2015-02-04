<?php
namespace PHPLivereload\Response;

use Symfony\Component\HttpFoundation;

/**
 * Description of Request
 *
 * @author ricky
 */
class Response extends HttpFoundation\Response
{
    
    public function setContentType($type, $charset)
    {
        $this->headers->set('Content-Type', "$type; charset=$charset");
    }

    public function __toString()
    {
        $this->headers->set('Content-Length', strlen($this->getContent()));

        return parent::__toString();
    }
}