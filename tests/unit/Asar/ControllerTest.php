<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Controller_Index extends Asar_Controller {
    
    
    protected $map = array(
        'path' => 'Test_Controller_Another',
        'next' => 'Test_Controller_Next'
    );
    
    function GET() {
        if ($this->request->getParam('change_template') == true) {
            $this->view->setTemplate('Test/View/Index/POST.html.php');
        } else {
            return 'hello there';
        }
    }
    
    function POST() {
        if ($this->request->getParam('change_template') == true) {
            $this->view->setTemplate('GET');
        } else {
            return 'I am alright';
        }
    }
}

class Test_Controller_Another extends Asar_Controller {
    function GET() {
        return 'hello world';
    }
}

class Test_Controller_Next extends Asar_Controller {
    protected $map = array(
        'proceed' => 'Test_Controller_Proceed',
        'follow'  => 'Test_Controller_Follow'
    );
}

class Test_Controller_Follow extends Asar_Controller {

    function GET() {
        $this->view['var'] = 'Followed GET';
    }
}

class Test_Controller_Forwarding extends Asar_Controller {
    protected $forward = 'Test_Controller_Forwarded';
    
    function POST() {
        $this->view['output'] = 'This is the way';
    }
}

class Test_Controller_Forwarded extends Asar_Controller {
    
    function POST()
    {
        $this->response->setType('txt');
    }
}

class Test_Controller_For_PostContents extends Asar_Controller {
    function POST() {
        return $this->data['var1'] . ' ' . $this->data['var2'];
    }
}

class Test_Controller_With_No_Methods extends Asar_Controller {}



class Asar_ControllerTest extends Asar_Test_Helper {
  
    protected function setUp() {
        $this->C = $this->getMock('Asar_Controller', array('GET'));
        $this->R = new Asar_Request;
        $this->R->setPath('/');
    }
    
    function testPassingARequestWithMethodGetInvokesGetMethod() {
        $controller = $this->getMock('Asar_Controller', array('GET'));
        $controller->expects($this->once())
                   ->method('GET')
                   ->will($this->returnValue('hello there'));
        
        $this->assertEquals(
            'hello there',
            $this->R->sendTo($controller)->__toString(),
            'Controller did not handle request'
        );
    }
    
    
    function testPassingARequestWithMethodGETShouldReturnStatusSuccessWhenRequestIsOkay() {
        $controller = $this->getMock('Asar_Controller', array('GET'));
        $controller->expects($this->once())
                   ->method('GET');
        $this->assertEquals(
            200,
            $this->R->sendTo($controller)->getStatus(),
            'Controller did not return proper status code'
        );
    }
    
    
    function testPassingARequestWithMethodPostShouldNotInvokeGetMethod() {
        $this->R->setMethod('POST');
        $controller = $this->getMock('Asar_Controller', array('GET', 'POST'));
        $controller->expects($this->never())
                   ->method('GET');
        $this->R->sendTo($controller);
    }
    
    function testPassingARequestWithMethodPostShouldInvokePostMethod() {
        $this->R->setMethod('POST');
        $controller = $this->getMock('Asar_Controller', array('GET', 'POST'));
        $controller->expects($this->once())
                   ->method('POST')
                   ->will($this->returnValue('Post method content.'));
        $this->assertEquals(
            'Post method content.',
            $this->R->sendTo($controller)->getContent(),
            'Controller did return expected response content.'
        );
    }
    
    function testPassingARequestWithMethodPutInvokesMappedMethod() {
        $this->R->setMethod('PUT');
        $controller = $this->getMock('Asar_Controller', array('PUT'));
        $controller->expects($this->once())
                   ->method('PUT')
                   ->will($this->returnValue('Put it on'));
        $this->assertEquals(
            'Put it on',
            $this->R->sendTo($controller)->__toString(),
            'Controller did not handle request'
        );
    }
    
    function testPassingARequestWithMethodDELETEInvokesMappedMethod() {
        $this->R->setMethod('DELETE');
        $controller = $this->getMock('Asar_Controller', array('DELETE'));
        $controller->expects($this->once())
                   ->method('DELETE')
                   ->will($this->returnValue('Test delete response'));
        $this->assertEquals(
            'Test delete response',
            $this->R->sendTo($controller)->__toString(),
            'Controller did not handle request'
        );
    }
    
    function testPassingARequestWithMethodHeadInvokesGetMethodButWillReturnAResponseWithoutContent() {
        $this->R->setMethod('HEAD');
        $controller = $this->getMock('Asar_Controller', array('GET'));
        $controller->expects($this->once())
                   ->method('GET')
                   ->will($this->returnValue('hello there'));
        
        $this->assertEquals(
            '',
            $this->R->sendTo($controller)->__toString(),
            'Controller should not output anything in the response body when responding a HEAD request'
        );
    }
    
    function testPassingARequestWithMethodHeadInvokesHeadMethodAndNotGetWhenThatHeadMethodIsDefinedInController() {
        $this->R->setMethod('HEAD');
        $controller = $this->getMock('Asar_Controller', array('GET', 'HEAD'));
        $controller->expects($this->once())
                   ->method('HEAD');
        $controller->expects($this->never())
                   ->method('GET');
        $this->assertEquals(
            '',
            $this->R->sendTo($controller)->__toString(),
            'Controller should not output anything in the response body when responding a HEAD request'
        );
    }
    
    protected function _testForUndefinedMethodsReturn405($method) {
        $this->R->setMethod($method);
        $controller = $this->getMock('Asar_Controller', array('donothing'));
        $this->R->sendTo($controller);
        $this->assertEquals(
            405,
            $controller->handleRequest($this->R)->getStatus(),
            "Controller did not return a response with expected 405 as status for method '$method' when that method was not defined"
        );
    }
    
    function testRequestingAMappedResourceButUndefinedMethodMustReturnA405StatusResponse() {
        $this->_testForUndefinedMethodsReturn405('PUT');
    }
    
    function testRequestingUndefinedGETMethod() {
        $this->_testForUndefinedMethodsReturn405('GET');
    }
    
    function testRequestingUndefinedPOSTMethod() {
        $this->_testForUndefinedMethodsReturn405('POST');
    }
    
    function testRequestingUndefinedDELETEMethod() {
        $this->_testForUndefinedMethodsReturn405('DELETE');
    }
    
    function testRequestingUndefinedHEADMethod() {
        $this->_testForUndefinedMethodsReturn405('HEAD');
    }
    
    protected function _randomClassNameGenerator($aClass = 'Controller') {
        do {
            $randomClassName = 'Amock_' . $aClass . '_' . substr(md5(microtime()), 0, 8);
        } while (class_exists($randomClassName, FALSE));
        return $randomClassName;
    }
    
    function testRequestingWithSubPaths() {
        $this->R->setMethod('GET');
        $this->R->setPath('/path/');
        
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "path" => "'. $cname2 .'"
                );
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                function GET() {
                    return "hello world";
                }
            }'
        );
        
        $controller = Asar::instantiate($cname1);
        $this->assertEquals('hello world', $this->R->sendTo($controller)->__toString(), 'Controller did not handle request');
    }
    
    function testGettingContext() {
        $this->R->setMethod('POST');
        $this->R->setPath('/next/');
        
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "next" => "'. $cname2 .'"
                );
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                function POST() {
                    return get_class($this->context);
                }
            }'
        );
        $controller = Asar::instantiate($cname1);
        $this->assertSame($cname1, $this->R->sendTo($controller)->__toString(), 'Unable to obtain context path');
    }
    
    function testGettingPathDepthWhenIndex() {
        $this->R->setMethod('PUT');
        $this->R->setPath('/');
        
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                function PUT() {
                    return $this->getDepth();
                }
            }'
        );
        $this->assertEquals('0', $this->R->sendTo( Asar::instantiate($cname1) )->__toString(), 'Controller was unable to obtain Path Depth');
    }
    
    function testGettingPathDepthWhen1LevelDeep() {
        $this->R->setMethod('POST');
        $this->R->setPath('/proceed');
        
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "proceed" => "'. $cname2 .'"
                );
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                function POST() {
                    return $this->getDepth();
                }
            }'
        );
        
        $this->assertEquals('1', $this->R->sendTo( Asar::instantiate($cname1) )->__toString(), 'Controller was unable to obtain Path Depth');
    }
    
    function testGettingPathDepthWhen2LevelsDeep() {
        $this->R->setMethod('POST');
        $this->R->setPath('/go/to');
        
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        $cname3 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "go" => "'. $cname2 .'"
                );
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                protected $map = array(
                    "to" => "'. $cname3 .'"
                );
            }
            
            class '. $cname3 . ' extends Asar_Controller {
                function POST() {
                    return $this->getDepth();
                }
            }'
        );
        
        $this->assertEquals('2', $this->R->sendTo( Asar::instantiate($cname1) )->__toString(), 'Controller was unable to obtain Path Depth');
    }
    
    function testMakeSureControllerActionHasAccessToRequestObject() {
        $controller = $this->getMock('Asar_Controller', array('GET'));
        $this->assertSame(null, $this->readAttribute($controller, 'request'), 'The request attribute was instantiated before request');
        $this->R->sendTo($controller);
        $this->assertSame($this->R, $this->readAttribute($controller, 'request'), 'The request attribute was not instantiated after request');
    }
    
    function testGettingPath() {
        $this->R->setMethod('POST');
        
        $cname1 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                function POST() {
                    return $this->getPath();
                }
            }'
        );
        
        $this->assertEquals('/', $this->R->sendTo( Asar::instantiate($cname1)  )->__toString(), 'Controller was unable to obtain Path');
    }
    
    function testRequestingAnUnmappedResourceResultsIn404StatusResponse() {
        $this->R->setPath('/non-existent-path/');
        
        $cname1 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                function GET() {}
            }'
        );
        
        $this->assertEquals(404, $this->R->sendTo( Asar::instantiate($cname1) )->getStatus());
    }
    
    function testRequestingAnUnmappedResourceButControllerHasForwardDefinedForwardsThatRequest() {
        $this->R->setPath('/we_are_the_champion/');
        
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $forward = "'. $cname2 .'";
                
                function GET() {
                    return "AAAA";
                }
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                function GET() {
                    return "BBBB";
                }
            }'
        );
        $this->assertEquals('BBBB', $this->R->sendTo( Asar::instantiate($cname1) )->__toString(), 'Unexpected response');
    }
    
    function testInitializeShouldRunFirstBeforeHandlingRequest() {        
        $cname1 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                private $testvar = "foo";
                
                protected function initialize() {
                    $this->testvar = "bar";
                }
                
                function GET() {
                    return $this->testvar;
                }
            }'
        );
        
        $this->assertEquals('bar', $this->R->sendTo( Asar::instantiate($cname1) )->__toString(), 'Unexpected response value');
    }
    
    function testUsingALocatorToRetrieveControllerToForwardToInMappedValues() {
        $nname = $this->_randomClassNameGenerator();
        $cname1 = $this->_randomClassNameGenerator();
        $cname_base = $this->_randomClassNameGenerator();
        $cname2 = 'Flock_' . $cname_base;
        
        $sample_code = 'class '. $nname . ' extends Asar_Locator {
            static function getLocator($context) {
                return new self;
            }
            
            function find($name) {
                return "Flock_".$name;
            }
        }
        
        class '. $cname1 . ' extends Asar_Controller {
            protected $map = array(
                "next" => "'. $cname_base .'"
            );
            
            protected function initialize() {
                $this->testvar = "bar";
                $this->setLocator("'. $nname .'");
            }
        }
        
        class '. $cname2 . ' extends Asar_Controller {
            function GET() {
                return "hello world!";
            }
        }';
        
        eval($sample_code);
        
        $this->R->setPath('/next/');
        $this->assertEquals(
            'hello world!', 
            $this->R->sendTo( Asar::instantiate($cname1) )->__toString(),
            'Unexpected response content. Did not locate the proper controller class to handle request'
        );
    }
    
    function testUsingALocatorToRetrieveControllerToForwardToInPlainForwards() {
        $nname = $this->_randomClassNameGenerator();
        $cname1 = $this->_randomClassNameGenerator();
        $cname_base = $this->_randomClassNameGenerator();
        $cname2 = 'Glock_' . $cname_base;
        
        $sample_code = 'class '. $nname . ' extends Asar_Locator {
            static function getLocator($context) {
                return new self;
            }
            
            function find($name) {
                return "Glock_".$name;
            }
        }
        
        class '. $cname1 . ' extends Asar_Controller {
            protected $forward = "'. $cname_base .'";
            
            protected function initialize() {
                $this->testvar = "bar";
                $this->setLocator("'. $nname .'");
            }
        }
        
        class '. $cname2 . ' extends Asar_Controller {
            function GET() {
                return "Another hello world!";
            }
        }';
        
        eval($sample_code);
        
        $this->R->setPath('/next/');
        $this->assertEquals(
            'Another hello world!', 
            $this->R->sendTo( Asar::instantiate($cname1) )->__toString(),
            'Unexpected response content. Did not locate the proper controller class to handle request'
        );
    }
    
    function testControllerInheritsLocatorOfTheForwardingController() {
        $nname = $this->_randomClassNameGenerator();
        $cname1 = $this->_randomClassNameGenerator();
        $cname2_base = $this->_randomClassNameGenerator();
        $cname2 = 'Glock_' . $cname2_base;
        $cname3_base = $this->_randomClassNameGenerator();
        $cname3 = 'Glock_' . $cname3_base;
        
        $sample_code = 'class '. $nname . ' extends Asar_Locator {
            static function getLocator($context) {
                return new self;
            }
            
            function find($name) {
                return "Glock_".$name;
            }
        }
        
        class '. $cname1 . ' extends Asar_Controller {
            protected $forward = "'. $cname2_base .'";
            
            protected function initialize() {
                $this->testvar = "bar";
                $this->setLocator("'. $nname .'");
            }
        }
        
        class '. $cname2 . ' extends Asar_Controller {
            protected $forward = "'. $cname3_base .'";
        }
        
        class '. $cname3 . ' extends Asar_Controller {
            function GET() {
                return "hola mundo!";
            }
        }
        
        ';
        
        eval($sample_code);
        
        $this->R->setPath('/next/path/');
        $this->assertEquals(
            'hola mundo!', 
            $this->R->sendTo( Asar::instantiate($cname1) )->__toString(),
            'Unexpected response content. Did not locate the proper controller class to handle request'
        );
    }
    
    function testGettingContextThroughMethod() {
        $obj = $this->getMock('Asar_Controller');
        $this->C->handleRequest($this->R, array('context' => $obj) );
        $this->assertSame($obj, $this->C->getContext(), 'Unable to retrieve context');
    }
    
    function testGettingUrl() {
        $this->R->setUri('http://example.org/');
        $this->R->sendTo($this->C);
        $this->assertEquals('http://example.org/', $this->C->url(), 'Unable to obtain url from controller');
    }
    
    function testGettingUrlFromDeeperController() {
        $this->R->setUri('http://example.org/path');
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "path" => "'. $cname2 .'"
                );
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                function GET() {
                    return $this->url();
                }
            }'
        );
        
        $content = $this->R->sendTo( Asar::instantiate($cname1) )->__toString();
        $this->assertEquals('http://example.org/path', $content, 
            'Unable to obtain url from a deeper controller');
    }
    
    function testGettingUrlFromRootControllerWhilePassingRequestToDeeperController() {
        $this->R->setUri('http://example.org/path');
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "path" => "'. $cname2 .'"
                );
            }

            class '. $cname2 . ' extends Asar_Controller {
                function GET() {
                    return $this->url();
                }
            }'
        );
        $controller = Asar::instantiate($cname1);
        $this->R->sendTo( $controller );
        $this->assertEquals('http://example.org/', $controller->url(), 'Unable to obtain url from a deeper controller');
    }
    
    function testWhenSettingTheStatusToNonDefaultInsideActionRespectIt()
    {
        $cname1 = $this->_randomClassNameGenerator();
        $cname2 = $this->_randomClassNameGenerator();
        
        eval (
            'class '. $cname1 . ' extends Asar_Controller {
                protected $map = array(
                    "path" => "'. $cname2 .'"
                );
            }
            
            class '. $cname2 . ' extends Asar_Controller {
                function GET() {
                    $this->response->setStatus($this->request->getParam("status"));
                }
            }'
        );
        
        $expected_status = rand(100, 599);
        $this->R->setUri('http://example.org/path');
        $this->R->setParam('status', $expected_status);
        $response = $this->R->sendTo(Asar::instantiate($cname1));
        $this->assertEquals($expected_status, $response->getStatus(),
            'The response did not return an expected status of ' . $expected_status);
    }
    
    function testExposingRequestParamsInController() {
        $this->R->setParam('var1', 'Foo');
        $this->R->setParam('var2', 'Bar');
        $this->R->sendTo($this->C);
        $expected_params = array('var1' => 'Foo', 'var2' => 'Bar');
        $this->assertEquals(
            $expected_params,
            $this->readAttribute($this->C, 'params'),
            'Unable to access request params through $this->params in controller'
        );
    }

    /*



    

    function testExposingRequestContentInController() {
        $postvariables = array('var1' => 'Foo', 'var2' => 'Bar');
        $this->R->setContent($postvariables);
        $this->R->setMethod('POST');
        $response = $this->R->sendTo(new Test_Controller_For_PostContents);
        $this->assertEquals(
            'Foo Bar',
            $response->__toString(),
            'Unable to access request data through $this->data in controller'
        );
    }
    

    function testSettingResponseTypeInController()
    {
        $this->R->setMethod('POST');
        $this->C = new Test_Controller_Forwarded;
        $response = $this->R->sendTo($this->C);
        $this->assertEquals('txt', $response->getType(), 'Response did not return expected response type');
    }
    
    function testResourcesWillAttemptToInvokeCorrespondingTemplateWhenViewIsDefined() {
        $this->R->setMethod('POST');
        $controller = new Test_Controller_Forwarding;
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $template = self::newFile('Test/View/Forwarding/POST.html.php', '<h1><?=$output?></h1>');
        $this->assertEquals('<h1>This is the way</h1>', $this->R->sendTo($controller)->__toString(), 'The template file was probably not invoked');
        set_include_path($old_include_path); // reset path
    }
    
    function testAttemptToInvokeCorrespondingTemplateGet() {
        $this->R->setMethod('GET');
        $this->R->setPath('/next/follow/');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $template = self::newFile('Test/View/Follow/GET.html.php', '<strong><?=$var?></strong>Yadayada');
        $this->assertEquals('<strong>Followed GET</strong>Yadayada', $this->R->sendTo($this->C)->__toString(), 'The template file was probably not invoked');
        set_include_path($old_include_path); // reset path
    }
    
    /**
     * Test Getting the Layout template
     *
     * @return void
     **//*
    public function testGetTheLayoutTemplateWhenItIsAvailable()
    {
        $this->R->setMethod('GET');
        $this->R->setPath('/next/follow/');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $template = self::newFile('Test/View/Follow/GET.html.php', '<h1><?=$var?></h1>');
        $layout   = self::newFile('Test/View/Layout.html.php', '<html><head><title>Test Layout</title></head><body><?=$contents?></body></html>');
        $this->assertEquals('<html><head><title>Test Layout</title></head><body><h1>Followed GET</h1></body></html>',
                            $this->R->sendTo($this->C)->__toString(),
                            'The layout file was not invoked');
        set_include_path($old_include_path); // reset path
    }
    
    /**
     * Test for getting the appropriate representation (view) for the request 
     *
     * @return void
     **//*
    public function testGettingJsonTemplateForTheRequest()
    {
        
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        // Json Representation
        $json_template = self::newFile('Test/View/Follow/GET.json.php', '{<?=$var?>}');
        $html_template = self::newFile('Test/View/Follow/GET.html.php', '<h1><?=$var?></h1>');
        $this->R->setPath('/next/follow/');
        $this->R->setType('json');
        $this->assertEquals('{Followed GET}',
                            $this->R->sendTo($this->C)->__toString(),
                            'The wrong template was used. Must be json.');
    }

    /**
     * Layout should not be included for requests for representations
     * other than html.
     *
     * @return void
     **//*
    public function testLayoutShouldNotBeIncludedWhenRequestDoesNotAskForHtmlRepresentation()
    {
        $this->R->setMethod('GET');
        $this->R->setPath('/next/follow/');
        $this->R->setType('json');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $template = self::newFile('Test/View/Follow/GET.json.php', '{<?=$var?>}');
        $layout   = self::newFile('Test/View/Layout.html.php', '<html><head><title>Test Layout</title></head><body><?=$contents?></body></html>');
        $this->assertNotContains('<html><head><title>Test Layout</title></head><body>',
                            $this->R->sendTo($this->C)->__toString(),
                            'The layout file was not invoked');
        set_include_path($old_include_path); // reset path
    }
    
    function testAttemptingToFindATxtTemplateWhentheRequestAcceptsATxtOnlyResponse()
    {
        $this->R->setMethod('GET');
        $this->R->setPath('/next/follow/');
        $this->R->setType('txt');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $template = self::newFile('Test/View/Follow/GET.txt.php', '{<?=$var?>}');
        $response = $this->R->sendTo($this->C);
        $this->assertContains('Followed GET',
                            $response->__toString(),
                            'Did not set the template variable');
        $this->assertEquals('txt', $response->getType(), 'Type did not match');
        set_include_path($old_include_path); // reset path
    }
    
    function testAttemptingToFindAnXmlTemplateWhentheRequestAcceptsAXmlOnlyResponse()
    {
        $this->R->setMethod('GET');
        $this->R->setPath('/next/follow/');
        $this->R->setType('xml');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $template = self::newFile('Test/View/Follow/GET.xml.php', '{<?=$var?>}');
        $response = $this->R->sendTo($this->C);
        $this->assertContains('Followed GET',
                            $response->__toString(),
                            'Did not set the template variable');
        $this->assertEquals('xml', $response->getType(), 'Type did not match');
        set_include_path($old_include_path); // reset path
    }
    
    function testRequestSends406StatusCodeWhenViewTemplateIsNotFoundForThatType()
    {
        $this->R->setMethod('GET');
        $this->R->setPath('/next/follow/');
        $this->R->setType('rss');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        $response = $this->R->sendTo($this->C);
        $this->assertEquals(406,
                            $response->getStatus(),
                            'Response did not return expected 406 response status');
        set_include_path($old_include_path); // reset path
    }
    
    function testSettingViewTemplate()
    {
        $this->R->setParam('change_template', true);
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        self::newFile('Test/View/Index/GET.html.php', 'Hello');
        self::newFile('Test/View/Index/POST.html.php', 'Yellow');
        $response = $this->R->sendTo($this->C);
        //$template = (self::readAttribute($this->C, 'view'));
        $this->assertEquals('Yellow', $response->__toString(), 'The controller did not use a different template');
        set_include_path($old_include_path); // reset path
    }
    
    function testSettingViewTemplateWithShortenedFileName()
    {
        $this->R->setParam('change_template', true);
        $this->R->setMethod('POST');
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        self::newFile('Test/View/Index/GET.html.php', 'Hello');
        self::newFile('Test/View/Index/POST.html.php', 'Yellow');
        $response = $this->R->sendTo($this->C);
        //$template = (self::readAttribute($this->C, 'view'));
        $this->assertEquals('Hello', $response->__toString(), 'The controller did not use a different template');
        set_include_path($old_include_path); // reset path
    }
    
    function testMakingControllerObjectAvailableOnTheView()
    {
        $this->R->setParam('change_template', true);
        $old_include_path = get_include_path();
        set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
        self::newFile('Test/View/Index/POST.html.php', 'Yellow');
        $this->R->sendTo($this->C);
        $template = (self::readAttribute($this->C, 'view'));
        $this->assertSame($this->C, $template->getController(), 'The controller was not set on the view template object');
        set_include_path($old_include_path); // reset path
    }
    
    */
}
