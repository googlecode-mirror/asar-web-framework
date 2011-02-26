<?php

class Asar_EnvironmentHelper {

  private $config, $request_factory, $response_exporter, $server_vars, $params, $post;

  function __construct(
    Asar_Config_Interface $config,
    Asar_RequestFactory $request_factory,
    Asar_ResponseExporter $response_exporter, $server_vars, $params, $post
  ) {
    $this->config = $config;
    $this->request_factory = $request_factory;
    $this->response_exporter = $response_exporter;
    $this->server_vars = $server_vars;
    $this->params = $params;
    $this->post   = $post;
  }
  
  function runTestEnvironment() {
    
  }
  
  function runAppInProductionEnvironment($app_name) {
    $app_scope = new Asar_ApplicationScope(
      $app_name, $this->config
    );
    $this->response_exporter->exportResponse(
      Asar_ApplicationInjector::injectApplicationRunner($app_scope)->run(
        $this->request_factory->createRequest(
          $this->server_vars, $this->params, $this->post
        )
      )
    );
  }
  
}
