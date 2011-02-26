<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

// TODO: How do we test factories?
class Asar_EnvironmentScopeTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->server  = array('foo' => 'bar'); //$_SERVER
    $this->get     = array('boo' => 'far'); //$_GET
    $this->post    = array('zoo' => 'jar'); //$_POST
    $this->files   = array('file' => array('info' => 'info')); //$_FILES
    $this->session = array('key' => 'value'); //... you get the idea...
    $this->cookie  = array('cookie_key' => 'cookie_value');
    $this->env     = array('var' => 'val');
    $this->dir     = '/foo';
    $this->scope = new Asar_EnvironmentScope(
      $this->server, $this->get, $this->post, $this->files,
      $this->session, $this->cookie, $this->env, $this->dir
    );
  }

  function testGettingServerVars() {
    $this->assertEquals($this->server, $this->scope->getServerVars());
  }
  
  function testGettingGetVars() {
    $this->assertEquals($this->get, $this->scope->getGetVars());
  }
  
  function testGettingPostVars() {
    $this->assertEquals($this->post, $this->scope->getPostVars());
  }
  
  function testGettingFilesVars() {
    $this->assertEquals($this->files, $this->scope->getFilesVars());
  }
  
  function testGettingSessionVars() {
    $this->assertEquals($this->session, $this->scope->getSessionVars());
  }
  
  function testGettingCookieVars() {
    $this->assertEquals($this->cookie, $this->scope->getCookieVars());
  }
  
  function testGettingEnvVars() {
    $this->assertEquals($this->env, $this->scope->getEnvVars());
  }

}
