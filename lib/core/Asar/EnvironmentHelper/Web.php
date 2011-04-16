<?php
/**
 * Environment helper that sets up the web server environment. This is
 * what is mostly used in the front controller.
 *
 * @package Asar
 * @subpackage core
 */
class Asar_EnvironmentHelper_Web {

  private 
    $config, $request_factory, $response_exporter,
    $server_vars, $params, $post;

  /**
   * @param Asar_Config_Interface $config
   * @param Asar_RequestFactory $request_factory
   * @param Asar_ResponseExporter $response_exporter
   */
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
  
  /**
   * Helper method for running the web server. This helper creates a scope
   * for an application specified by $app_name passing all the necessary
   * parameters for needed to make it accept a request. CONFUSING.
   *
   * @param string $app_name name of the app or the class name of the
   *                         application class without the "_Application" suffix
   */
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
