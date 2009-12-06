<?php
require_once realpath(dirname(__FILE__). '/../../config.php');
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_ClientTest extends Asar_Test_Helper
{
    
    public function setUp()
    {
        $this->client  = new Asar_Client;
        $this->app = $this->getMock('Asar_Application', array('handleRequest'));
        $this->client->setServer($this->app);
    }
    
    public function testClientShouldSetServer()
    {
        
        $this->assertSame(
            $this->app,
            $this->client->getServer(),
            'The client was not able to set the current server'
        );
    }
    
    // TODO: Maybe we can use dependency injection instead
    public function testGetAnythingShouldSendTheCorrectAsarRequestObjectToServer()
    {
        $this->app->expects($this->once())
             ->method('handleRequest')
             ->will($this->returnCallBack(array($this, 'getFirstArg')));
            
        $this->client->setServer($this->app);
        $this->client->GET('/');
        $R = $this->getObject('arg');
        $this->assertTrue(
             $R instanceof Asar_Request_Interface,
            "Passed argument not an instance of Asar_Request_Interface"
        );
        $this->assertEquals(
            '/', $R->getPath(),
            'Path was not properly set'
        );
        $this->assertEquals(
            'GET', $R->getMethod(),
            'Request method is not GET.'
        );
    }
    
    public function getFirstArg()
    {
        $args = func_get_args();
        $this->saveObject('arg', $args[0]);
    }
    
    public function testGetWithPathShouldSendRequestWithThatPath()
    {
        $this->app->expects($this->once())
             ->method('handleRequest')
             ->will($this->returnCallBack(array($this, 'getFirstArg')));
            
        $this->client->setServer($this->app);
        $this->client->GET('/what');
        $this->assertEquals(
            '/what', $this->getObject('arg')->getPath(),
            'Path was not properly set'
        );
    }
    
    public function testPostShouldSendRequestWithPostMethod()
    {
        $this->app->expects($this->once())
             ->method('handleRequest')
             ->will($this->returnCallBack(array($this, 'getFirstArg')));
            
        $this->client->setServer($this->app);
        $this->client->POST('/somewhere', array('foo' => 'bar'));
        $R = $this->getObject('arg');
        $this->assertTrue(
             $R instanceof Asar_Request_Interface,
            "Passed argument not an instance of Asar_Request"
        );
        $this->assertEquals(
            '/somewhere', $R->getPath(),
            'Path was not properly set'
        );
        $this->assertEquals(
            'POST', $R->getMethod(),
            'Request method is not POST.'
        );
        $this->assertEquals(
            array('foo'=>'bar'), $R->getContent(),
            "The content of the request wasn't set."
        );
    }
    
    public function testSendingGetRequestWithParameters()
    {
        $this->app->expects($this->once())
             ->method('handleRequest')
             ->will($this->returnCallBack(array($this, 'getFirstArg')));
            
        $this->client->setServer($this->app);
        $this->client->GET('/', array('var1' => 'value1', 'var2' => 'value2'));
        $params = $this->getObject('arg')->getParams();
        $this->assertEquals(
            'value1', $params['var1'],
            "First param was not found on request params."
        );
        $this->assertEquals(
            'value2', $params['var2'],
            "Second param was not found on request params."
        );
    }
    
    public function testSendingRequestType()
    {
        $this->app->expects($this->once())
             ->method('handleRequest')
             ->will($this->returnCallBack(array($this, 'getFirstArg')));
            
        $this->client->setServer($this->app);
        $this->client->GET('/', array(), array('Accept' =>'application/xml'));
        $R = $this->getObject('arg');
        $this->assertTrue(
             $R instanceof Asar_Request_Interface,
            "Passed argument not an instance of Asar_Request"
        );
        $this->assertEquals(
            'application/xml', $R->getHeader('Accept'),
            "The content-type of the request wasn't set."
        );
    }
    
    
}

