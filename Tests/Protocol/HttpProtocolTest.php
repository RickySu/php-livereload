<?php
namespace PHPLivereload\Tests\Protocol;

use PHPLivereload\Protocol\HttpProtocol;

/**
 * Description of Message
 *
 * @author ricky
 */
class HttpProtocolTest extends \PHPUnit_Framework_TestCase
{
    public function test_construct()
    {
        $calls = [];
        $app = $this->getMockBuilder('\\PHPLivereload\\Application\\ServerApplication')
                ->disableOriginalConstructor()
                ->getMock();
        $socket = $this->getMockBuilder('\\React\\Socket\\Server')
                ->disableOriginalConstructor()
                ->getMock();
        $httpProtocol = $this->getMockBuilder('\\PHPLivereload\\Protocol\\HttpProtocol')
             ->setMethods(array('initEvent'))
             ->disableOriginalConstructor()
             ->getMock();
        $httpProtocol->expects($this->any())
            ->method('initEvent')
            ->will($this->returnCallback(function() use(&$calls){
                $calls[] = 'initEvent';
            }));

        $reflectedClass = new \ReflectionClass($httpProtocol);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($httpProtocol, $socket, $app);
        $this->assertEquals($app, $this->getObjectAttribute($httpProtocol, 'app'));
        $this->assertEquals(array('initEvent'), $calls);
    }

    public function test_initEvent()
    {
        $calls = [];
        $socketConn = $this->getMockBuilder('\\React\\Socket\\Connection')
                ->disableOriginalConstructor()
                ->getMock();
        $socket = $this->getMockBuilder('\\React\\Socket\\Server')
                ->setMethods(array('on'))
                ->disableOriginalConstructor()
                ->getMock();
        $socket->expects($this->any())
            ->method('on')
            ->will($this->returnCallback(function($event, $callback) use(&$calls, $socketConn){
                $calls[] = 'on';
                $callback($socketConn);
                $this->assertEquals('connection', $event);
            }));

        $httpProtocol = $this->getMockBuilder('\\PHPLivereload\\Protocol\\HttpProtocol')
             ->setMethods(array('onConnect'))
             ->disableOriginalConstructor()
             ->getMock();
        $httpProtocol->expects($this->any())
            ->method('onConnect')
            ->will($this->returnCallback(function($conn) use(&$calls, $socketConn){
                $calls[] = 'onConnect';
                $this->assertEquals($socketConn, $conn);
            }));

        $reflectedClass = new \ReflectionClass($httpProtocol);
        $method = $reflectedClass->getMethod('initEvent');
        $method->setAccessible(true);
        $method->invoke($httpProtocol, $socket);
        $this->assertEquals(array('on', 'onConnect'), $calls);

    }
}
