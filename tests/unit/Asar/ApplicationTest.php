<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar/Application.php';


class Test2_Application extends Asar_Application {}

class Test2_Router extends Asar_Router {}


class Asar_ApplicationTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->app = new Test2_Application();
  }
  
  
  function testLoadingController() {
    $test     = 'cheap';
    $expected = 'Test2_Controller_Cheap';
    try {
      $this->app->loadController($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load controller');
    }
  }
  
  
  function testLoadingModel() {
    $test     = 'cheap';
    $expected = 'Test2_Model_Cheap';
    try {
      $this->app->loadModel($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load model');
    }
  }
  
  
  function testLoadingFilter() {
    $test     = 'cheap';
    $expected = 'Test2_Filter_Cheap';
    try {
      $this->app->loadFilter($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load filter');
    }
  }
  
  
  function testLoadingHelper() {
    $test     = 'cheap';
    $expected = 'Test2_Helper_Cheap';
    try {
      $this->app->loadHelper($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load helper');
    }
  }
  
  
  function testLoadingViewWithoutAction() {
    $test     = 'cheap';
    $expected = 'Test2/View/cheap.php';
    $this->assertEquals($expected, $this->app->loadView($test), 'Returned view file is wrong');
  }
  
  
  function testLoadingViewWithAction() {
    $test_controller     = 'cheap';
    $test_action         = 'shot';
    $expected            = 'Test2/View/cheap/shot.php';
    $this->assertEquals($expected, $this->app->loadView($test_controller, $test_action), 'Returned view file is wrong');
  }
  
  
  function testGetRouter() {
    $expected = 'Test2_Router';
    
  }
  
  
  function testProcessingRequest() {
    //$req = 
    $this->markTestIncomplete('not ready');
  }
}

?>