<?php
namespace PHPLivereload\Tests\Application;

use PHPLivereload\Application\ServerApplication;

class MockServerApplication extends ServerApplication
{
    public function __construct($host = '127.0.0.1', $port = 35729)
    {
    }
}

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

    public function test_run()
    {
        $calls = array();
        $app = new MockServerApplication();
        $loop = $this->getMock('\\React\\EventLoop\\StreamSelectLoop', array('run'));
        $loop->expects($this->any())
            ->method('run')
            ->will($this->returnCallback(function() use(&$calls){
                $calls[] = 'run';
            }));
        $reflectedClass = new \ReflectionClass($app);
        $loopProperty = $reflectedClass->getProperty('loop');
        $loopProperty->setAccessible(true);
        $loopProperty->setValue($app, $loop);
        $app->run();
        $this->assertEquals(array('run'), $calls);
    }

    public function test_getConfig()
    {
        $config = md5(microtime().rand());
        $app = new MockServerApplication();
        $reflectedClass = new \ReflectionClass($app);
        $configProperty = $reflectedClass->getProperty('config');
        $configProperty->setAccessible(true);
        $configProperty->setValue($app, $config);
        $this->assertEquals($config, $app->getConfig());
    }

    public function test_initLoop()
    {
        $app = new MockServerApplication();
        $reflectedClass = new \ReflectionClass($app);
        $initLoop = $reflectedClass->getMethod('initLoop');
        $initLoop->setAccessible(true);
        $initLoop->invoke($app);
        $loopProperty = $reflectedClass->getProperty('loop');
        $loopProperty->setAccessible(true);
        $loop = $loopProperty->getValue($app);
        $this->assertTrue($loop instanceof \React\EventLoop\LoopInterface);
    }

    public function test_addClient()
    {
        $client1 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->getMock();
        $client2 = clone $client1;
        $app = new MockServerApplication();
        $app->addClient($client1);
        $reflectedClass = new \ReflectionClass($app);
        $clientsProperty = $reflectedClass->getProperty('clients');
        $clientsProperty->setAccessible(true);
        $clients = $clientsProperty->getValue($app);
        $this->assertEquals(array($client1), $clients);
        $app->addClient($client2);
        $clients = $clientsProperty->getValue($app);
        $this->assertEquals(array($client1, $client2), $clients);
        $app->addClient($client1);
        $clients = $clientsProperty->getValue($app);
        $this->assertEquals(array($client1, $client2), $clients);
    }

    public function test_removeClient()
    {
        $client1 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->setMockClassName('client1')
                ->getMock();
        $client2 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->setMockClassName('client2')
                ->getMock();
        $client3 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->setMockClassName('client3')
                ->getMock();
        $app = new MockServerApplication();
        $app->addClient($client1);
        $app->addClient($client2);
        $app->addClient($client3);
        $app->removeClient($client2);
        $reflectedClass = new \ReflectionClass($app);
        $clientsProperty = $reflectedClass->getProperty('clients');
        $clientsProperty->setAccessible(true);
        $clients = $clientsProperty->getValue($app);
        $this->assertEquals(array($client1, $client3), array_values($clients));
    }

    public function test_reloadFile()
    {
        $calls = array();
        $file = md5(microtime().rand());
        $client1 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->setMockClassName('client1')
                ->setMethods(array('reload'))
                ->getMock();
        $client1->expects($this->any())
            ->method('reload')
            ->will($this->returnCallback(function() use(&$calls){
                $calls[] = 'client1';
            }));
        $client2 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->setMockClassName('client2')
                ->setMethods(array('reload'))
                ->getMock();
        $client2->expects($this->any())
            ->method('reload')
            ->will($this->returnCallback(function() use(&$calls){
                $calls[] = 'client2';
            }));
        $client3 = $this->getMockBuilder('\\PHPLivereload\\Protocol\\LivereloadProtocol')
                ->disableOriginalConstructor()
                ->setMockClassName('client3')
                ->setMethods(array('reload'))
                ->getMock();
        $client3->expects($this->any())
            ->method('reload')
            ->will($this->returnCallback(function() use(&$calls){
                $calls[] = 'client3';
            }));
        $app = new MockServerApplication();
        $app->addClient($client1);
        $app->addClient($client2);
        $app->addClient($client3);
        $app->reloadFile($file);
        $this->assertEquals(array('client1', 'client2', 'client3'), $calls);
    }
}
