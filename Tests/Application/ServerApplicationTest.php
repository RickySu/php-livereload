<?php
namespace PHPLivereload\Tests\Application;

use PHPLivereload\Application;

class ServerApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function test___construct()
    {
        $host = '127.0.0.1';
        $port = 12345;
        $calls = array();
        $app = $this->getMockBuilder('\\PHPLivereload\\Application\\ServerApplication')
                ->disableOriginalConstructor()
                ->setMethods(array('initLoop', 'initServer'))
                ->getMock();
        $app->expects($this->any())
            ->method('initLoop')
            ->will($this->returnCallback(function() use(&$calls){
                $calls[] = 'initLoop';
            }));
        $app->expects($this->any())
            ->method('initServer')
            ->will($this->returnCallback(function($hostForTest, $portForTest) use(&$calls, $host, $port){
                $calls[] = 'initServer';
                $this->assertEquals($host, $hostForTest);
                $this->assertEquals($port, $portForTest);
            }));

          $reflectedClass = new \ReflectionClass($app);
          $constructor = $reflectedClass->getConstructor();
          $constructor->invoke($app, $host, $port);
          $this->assertEquals(array('initLoop', 'initServer'), $calls);
    }
}
