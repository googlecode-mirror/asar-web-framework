<?php
require_once realpath(dirname(__FILE__). '/../../config.php');

// This class is for testing purposes only
class Asar_ResourceTest_DummyTemplateEngine implements Asar_Template_Interface
{
    public function render() {}
    
    public function __set($variable, $value = null) {}
    
    public function set($variable, $value = null) {}
    
    public function setTemplateFile($file) {}
    
    public function setLayout($file) {}
    
    public function getTemplateFile() {}
    
    public function getTemplateFileExtension() {}
}

class Asar_ResourceTest extends Asar_Test_Helper {

  private static $prefCount = 0;

    public function setUp()
    {
        $this->R = new Asar_Resource;
    }
    
    public function testShouldBeAbleToSetRequestAttribute()
    {
        $this->request = new Asar_Request;
        $this->R = $this->getMock('Asar_Resource', array('GET'));
        $this->R->expects($this->once())
            ->method('GET')
            ->will($this->returnCallBack(array($this, 'checkRequestAttribute')));
        $this->R->handleRequest($this->request);
    }
    
    public function checkRequestAttribute()
    {
        $this->assertSame(
            $this->request,
            $this->readAttribute($this->R, 'request'),
            'Unable to set request attribute in resource object.'
        );
    }
    
    public function testResponseShouldProcessGetRequest()
    {
        $request = new Asar_Request;
        $request->setMethod('GET');
        $R = $this->getMock('Asar_Resource', array('GET'));
        $R->expects($this->once())
            ->method('GET')
            ->will($this->returnValue('hello world'));
        $response = $R->handleRequest($request);
        $this->assertEquals(
            'hello world', $response->getContent(),
            'Unable to set content for response'
        );
    }
    
    public function testResponseShouldBe200WhenNormalProcessingOfGetRequest()
    {
        $request = new Asar_Request;
        $request->setMethod('GET');
        $R = $this->getMock('Asar_Resource', array('GET'));
        $R->expects($this->once())
            ->method('GET')
            ->will($this->returnValue('hello world'));
        $response = $R->handleRequest($request);
        $this->assertEquals(
            200, $response->getStatus(),
            'Unable to set 200 status for response'
        );
    }
    
    public function testResponseContentTypeShouldBeHtmlByDefaultGetRequest()
    {
        $request = new Asar_Request;
        $request->setMethod('GET');
        $R = $this->getMock('Asar_Resource', array('GET'));
        $R->expects($this->once())
            ->method('GET')
            ->will($this->returnValue('hello world'));
        $response = $R->handleRequest($request);
        $this->assertContains(
            'text/html', $response->getHeader('Content-Type'),
            'Content-type of response does not contain "text/html"'
        );
        $this->assertEquals(
            0, strpos($response->getHeader('Content-Type'), 'text/html'),
            'Unable to set default content-type of "text/html" for response'
        );
    }
    
    public function testExecutePOSTMethodWhenPostRequest()
    {
        $request = new Asar_Request;
        $request->setMethod('POST');
        $R = $this->getMock('Asar_Resource', array('POST'));
        $R->expects($this->once())
            ->method('POST')
            ->will($this->returnValue('hello post world'));
        $response = $R->handleRequest($request);
        $this->assertEquals(
            'hello post world', $response->getContent(),
            'Unable to set content for response'
        );
        $this->assertEquals(
            200, $response->getStatus(),
            'Unable to set content for response'
        );
    }
    
    public function testExecutingPOSTMethodSetsContentOfPostGlobalVariable()
    {
        $request = new Asar_Request;
        $request->setMethod('POST');
        $request->setContent(array('foo'=>'bar', 'one' => 1));
        $R = $this->getMock('Asar_Resource', array('POST'));
        $response = $R->handleRequest($request);
        $this->assertEquals(
            'bar', $_POST['foo'],
            'Unable to set post variable'
        );
        $this->assertEquals(
            1, $_POST['one'],
            'Unable to set post variable'
        );
        $_POST = array();
    }
    
    public function testResourceWithoutDefinedHttpMethodShouldReturn405HttpStatus()
    {
        $cname = get_class($this) . '_ResourceStatus405';
        eval('
            class '. $cname . ' extends Asar_Resource {}
        ');
        $R = Asar::instantiate($cname);
        $request = new Asar_Request;
        foreach (array('GET', 'POST', 'PUT', 'DELETE') as $method) {
            $request->setMethod($method);
            $response = $R->handleRequest($request);
            $this->assertEquals(
                405, $response->getStatus(),
                'The HTTP Response Status is not 405 for undefined method ' .
                    $method . '.'
            );
        }
    }
    
    public function testResourceShouldSendResponse500StatusWhenMethodRaisesException()
    {
        $R = $this->getMock('Asar_Resource', array('GET'));
        $R->expects($this->once())
            ->method('GET')
            ->will($this->returnCallBack(array($this, 'checkRaisingException')));
        $response = $R->handleRequest(new Asar_Request);
        $this->assertEquals(
            500, $response->getStatus(),
            'The HTTP Response Status is not 500 for method throwing an Exception'
        );
    }
    
    public function checkRaisingException()
    {
        throw new Exception;
    }
    
    public function testResourceShouldShowErrorMessageByDefaultFor500Response()
    {
        $R = $this->getMock('Asar_Resource', array('GET'));
        $R->expects($this->once())
            ->method('GET')
            ->will($this->returnCallBack(array($this, 'checkRaisingExceptionMsg')));
        $response = $R->handleRequest(new Asar_Request);
        $this->assertContains(
            'The error message.', $response->getContent(),
            'The Resource did not set the error message in content by default.'
        );
    }
    
    public function checkRaisingExceptionMsg()
    {
        throw new Exception('The error message.');
    }
    
    public function testResourceTriggersErrorWhenTryingToAccessUnknownProperty()
    {
        set_error_handler(array($this, 'temporaryErrorHandler'));
        $this->R->something;
        restore_error_handler();
        $this->assertNotNull(
            $this->getObject('error'),
            'Accessing unknown property did not trigger any error.'
        );
        $error = $this->getObject('error');
        $this->assertContains(
            'Unknown property \'something\' via __get()', $error[1],
            'Wrong error message sent.'
        );
        
    }
    
    public function temporaryErrorHandler($err_no, $err_str)
    {
        $this->saveObject(
            'error', array($err_no, $err_str)
        );
    }
    
    public function testResourceAttemptsToRenderTemplateWhenTemplateIsSet()
    {
        $this->R->template = $this->getMock('Asar_Template_Interface', array());
        $this->R->template->expects($this->once())
          ->method('render');
        $this->R->handleRequest(new Asar_Request);
    }
    
    public function testResourceAttemptsToRenderTemplateWhenTemplateIsSetPOST()
    {
        $this->R = new Asar_Resource;
        $this->R->template = $this->getMock('Asar_Template_Interface', array());
        $this->R->template->expects($this->once())
          ->method('render');
        $req = new Asar_Request;
        $req->setMethod('POST');
        $this->R->handleRequest($req);
    }
    
    public function testSettingTemplateEngine()
    {
        $pref = get_class($this);
        $cname = $pref . '_DummyEngine';
        eval('
            class '. $cname . ' extends ' . 
            $pref . '_DummyTemplateEngine {}
        ');
        $this->R->setTemplateEngine($cname);
        $this->R->template->any = 'something';
        $this->assertEquals(
            $cname, get_class($this->R->template),
            'Unable to set the Template Engine'
        );
    }
    
    public function testResourceDefaultsToAsarTemplateEngine()
    {
        $this->R->template->any = 'something';
        $this->assertEquals(
            'Asar_Template', get_class($this->R->template),
            'Unable to set the Template Engine'
        );
    }
    
    public function testSettingConfigurationOnResource()
    {
        $tpldir = self::getTempDir().'Hehe/';
        $hlpdir = self::getTempDir().'Wawa/';
        $this->R->setConfiguration(array(
            'default_representation_dir' => $tpldir,
            'default_helper_dir' => $hlpdir
        ));
        $conf = $this->R->getConfiguration();
        $this->assertEquals(
            $tpldir, $conf['default_representation_dir'],
            'Unable to set a configuration directive'
        );
        $this->assertEquals(
            $hlpdir, $conf['default_helper_dir'],
            'Unable to set a configuration directive'
        );
    }
    
    public function testSettingConfigurationMultipleTimes()
    {
        $tpldir = self::getTempDir().'Nana/';
        $hlpdir = self::getTempDir().'Yaya/';
        $tpldir2 = self::getTempDir().'Hehe2/';
        $this->R->setConfiguration(array(
            'default_representation_dir' => $tpldir,
            'default_helper_dir' => $hlpdir
        ));
        $this->R->setConfiguration(array(
            'default_representation_dir' => $tpldir2
        ));
            
        $conf = $this->R->getConfiguration();
        $this->assertEquals(
            $tpldir2, $conf['default_representation_dir'],
            'Unable to set a configuration directive'
        );
        $this->assertEquals(
            $hlpdir, $conf['default_helper_dir'],
            'Unable to set a configuration directive'
        );
    }
    
    public function testSettingContextSetsDefaultRepresentationDir()
    {
        $context = $this->createAppMock();
        $reflector = new ReflectionClass(get_class($context));
        $representation_dir = dirname($reflector->getFileName()) .
            DIRECTORY_SEPARATOR . 'Representation';
        $this->R->setConfiguration( array('context' => $context) );
        
        $conf = $this->R->getConfiguration();
        $this->assertEquals(
            $representation_dir, $conf['default_representation_dir'],
            'Unable to set a default representation dir after context was set.'
        );
    }
    
    public function testSettingDefaultRepresentationDirSetsTemplateLocation()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix);
        $dir = Asar::constructPath('Somewhere','In','The','FS');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Index', 'GET.html.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $R->handleRequest(new Asar_Request);
    }
    
    private function generatePrefix() {
      return get_class($this). '_App' . (++self::$prefCount);
    }
    
    private function createAppMock($prefix = null) {
      return $this->getMock(
        'stdClass', array(), array(), ($prefix ? $prefix . '_Application' : '') 
      );
    }
    
    private function createResourceMock($prefix, $name = 'Index') {
      return $this->getMock(
        'Asar_Resource', array('GET'), array(),
        $prefix . '_Resource_' . $name
      );
    }
    
    public function testSettingDefaultRepresentationDirSetsTemplateLocation2()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix, 'Vindex');
        $dir = Asar::constructPath('Over','There','Here');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Vindex', 'GET.html.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $R->handleRequest(new Asar_Request);
    }
    
    public function testSettingDefaultRepresentationDirSetsTemplateLocation3()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix, 'Foo_Bar');
        $dir = Asar::constructPath('Over','There','Here');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Foo', 'Bar', 'GET.html.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $R->handleRequest(new Asar_Request);
    }
    
    public function testSettingTemplateSetsLayoutByDefault()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix, 'Foo_Bar');
        $dir = Asar::constructPath('Somewhere','There');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setLayout')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Layout.html.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $R->handleRequest(new Asar_Request);
    }
    
    public function testSettingTemplateSetsLayoutAccordingToFileType()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix);
        $dir = Asar::constructPath('Another','Directory');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setLayout')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Layout.txt.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $request = new Asar_Request;
        $request->setHeader('Accept', 'text/plain');
        $R->handleRequest($request);
    }
    
    public function testSettingTemplateForPostRequest()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix);
        $dir = Asar::constructPath('Over','There','Here');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Index', 'POST.html.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $request = new Asar_Request;
        $request->setMethod('POST');
        $R->handleRequest($request);
    }
    
    public function testSettingTemplateTxtContentTypeRequests()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix);
        $dir = Asar::constructPath('Where');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Index', 'GET.txt.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $request = new Asar_Request;
        $request->setHeader('Accept', 'text/plain');
        $response = $R->handleRequest($request);
        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
    }
    
    public function testSettingTemplateXmlContentTypeRequests()
    {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix);
        $dir = Asar::constructPath('Where');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Index', 'GET.xml.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $request = new Asar_Request;
        $request->setHeader('Accept', 'application/xml');
        $response = $R->handleRequest($request);
        $this->assertEquals(
          'application/xml',
          $response->getHeader('Content-Type')
        );
    }
    
    public function testAlternativeTemplateFileWhenItExists() {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix);
        $dir = self::createDir('Representation');
        self::newFile('Representation/Index.GET.html.php', '');
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('setTemplateFile')
            ->with($this->equalTo(
                Asar::constructPath($dir, 'Index.GET.html.php')
            ));
        $R->setConfiguration(array(
            'context' => $context, 
            'default_representation_dir' => $dir
        ));
        $R->template = $template;
        $R->handleRequest(new Asar_Request);
    }
    
    public function testUseAlternativeTemplateFileWhenItExists2() {
      $prefix = $this->generatePrefix();
      $context = $this->createAppMock($prefix);
      $R = $this->createResourceMock($prefix, 'Foo_Bar_Index');
      $dir = self::createDir('Representation');
      self::newFile('Representation/Foo/Bar/Index.GET.html.php', '');
      $template = $this->getMock('Asar_Template_Interface');
      $template->expects($this->once())
          ->method('setTemplateFile')
          ->with($this->equalTo(
              Asar::constructPath($dir, 'Foo/Bar/Index.GET.html.php')
          ));
      $R->setConfiguration(array(
          'context' => $context, 
          'default_representation_dir' => $dir
      ));
      $R->template = $template;
      $R->handleRequest(new Asar_Request);
    }
    
    public function testSetStatusTo406WhenTemplateThrowsFileNotFoundException() {
        $prefix = $this->generatePrefix();
        $context = $this->createAppMock($prefix);
        $R = $this->createResourceMock($prefix, 'Index');
        $expected_exception = $this->getMock(
          'Asar_Template_Exception_FileNotFound', array('__toString')
        );
        $template = $this->getMock('Asar_Template_Interface');
        $template->expects($this->once())
            ->method('render')
            ->will($this->throwException($expected_exception));
        $R->setConfiguration(array(
            'context' => $context
        ));
        $R->template = $template;
        $response = $R->handleRequest(new Asar_Request);
        $this->assertEquals(
          406, $response->getStatus()
        );
    }
    
    public function testSetStatus406ForRequestsForUnknownTypes() {
      $request = new Asar_Request;
      $request->setHeader('Accept', 'unknown/type');
      $R = $this->getMock('Asar_Resource', array('GET'));
      $response = $R->handleRequest($request);
      $this->assertEquals(
        406, $response->getStatus()
      );
    }
    
}
