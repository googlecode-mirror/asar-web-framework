<?php

class Asar_EnvironmentHelper {

  private $request_factory, $response_exporter, $app_factory, $server, $params, $post;

  function __construct($request_factory, $response_exporter, $app_factory, $server, $params, $post) {
    $this->request_factory = $request_factory;
    $this->response_exporter = $response_exporter;
    $this->app_factory = $app_factory;
    $this->server = $server;
    $this->params = $params;
    $this->post   = $post;
  }
  
  function runTestEnvironment() {
    
  }
  
  function runAppInProductionEnvironment($app) {
    $this->response_exporter->exportResponse(
      $this->app_factory->getApplication($app)->handleRequest(
        $this->request_factory->createRequest(
          $this->server, $this->params, $this->post
        )
      )
    );
  }
  
}
