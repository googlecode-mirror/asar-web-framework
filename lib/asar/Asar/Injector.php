<?php

namespace Asar;

class Injector extends \Pimple {
  
  function __construct(
    $server = array(), $get = array(), $post = array(), $files = array(), 
    $session = array(), $cookie = array(), $env = array()
  ) {
    $this->server  = $server;
    $this->get     = $get;
    $this->post    = $post;
    $this->files   = $files;
    $this->session = $session;
    $this->cookie  = $cookie;
    $this->env     = $env;
    $this->defineGraph();
  }
  
  private function defineGraph() {
  
    $this->ApplicationRunner = function(\Pimple $c) {
      return new ApplicationRunner;
    };
    
    $this->EnvironmentHelper = function(\Pimple $c) {
      return new EnvironmentHelper\Web(
        $c->DefaultConfig,
        $c->RequestFactory,
        $c->ResponseExporter,
        $c->server,
        $c->get,
        $c->post
      );
    };
    
    $this->ClassLoader = function(\Pimple $c) {
      return new ClassLoader;
    };
    
    $this->RequestFactory = function(\Pimple $c) {
      return new RequestFactory;
    };
    
    $this->ResponseExporter = function(\Pimple $c) {
      return new ResponseExporter;
    };
    
    $this->ApplicationFactory = function(\Pimple $c) {
      return new ApplicationFactory(
        $c->DefaultConfig
      );
    };
    
    $this->FileSearcher = function(\Pimple $c) {
      return new FileSearcher;
    };
    
    $this->FileIncludeManager = function(\Pimple $c) {
      return new FileIncludeManager;
    };
    
    $this->DefaultConfig = function(\Pimple $c) {
      return new Config\DefaultConfig;
    };
  }
  
}
