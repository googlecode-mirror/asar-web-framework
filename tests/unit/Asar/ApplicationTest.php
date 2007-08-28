<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar/Application.php';

class Test_Application extends Asar_Application {}

class Asar_ApplicationTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->app = new Test_Application();
  }
  
  function testApplicationStart() {
    $this->markTestIncomplete('Not yet implemented');
  }
  
  /*
  function testGetResourcePath() {
    $this->assertEquals('test_path/', $this->app->getResourcePath(), 'Path set did not match');
    $this->assertEquals('test_path/models/', $this->app->getModelsPath(), 'Path set did not match');
    $this->assertEquals('test_path/controllers/', $this->app->getControllersPath(), 'Path set did not match');
    $this->assertEquals('test_path/views/', $this->app->getViewsPath(), 'Path set did not match');
    $this->assertEquals('test_path/helpers/', $this->app->getHelpersPath(), 'Path set did not match');
    $this->assertEquals('test_path/filters/', $this->app->getFiltersPath(), 'Path set did not match');
  }
  */
  
  function testLoadingController() {
    $test     = 'cheap';
    $expected = 'Test_Controller_Cheap';
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
    $expected = 'Test_Model_Cheap';
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
    $expected = 'Test_Filter_Cheap';
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
    $expected = 'Test_Helper_Cheap';
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
    $expected = 'Test/View/cheap.php';
    $this->assertEquals($expected, $this->app->loadView($test), 'Returned view file is wrong');
  }
  
  function testLoadingViewWithAction() {
    $test_controller     = 'cheap';
    $test_action         = 'shot';
    $expected            = 'Test/View/cheap/shot.php';
    $this->assertEquals($expected, $this->app->loadView($test_controller, $test_action), 'Returned view file is wrong');
  }
  
}

?>