<?php

class Asar_ApplicationRunner {

  private $app, $request_factory, $app_factory, $server, $params, $post;

  function __construct($app, $response_exporter, $app_factory, $server, $params, $post) {
    $this->app = $app;
    $this->request_factory = $request_factory;
    $this->app_factory = $app_factory;
    $this->server = $server;
    $this->params = $params;
    $this->post   = $post;
  }
  
  function run() {
    ($this->app, $this->$config)
    $this->app_factory->getApplication($app)->handleRequest(
      $this->request_factory->createRequest(
        $this->server, $this->params, $this->post
      )
    )
  }
  
}