<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_ApplicationRunner {

  private $app;

  function __construct(Asar_Application $app) {
    $this->app = $app;
  }
  
  function run(Asar_Request_Interface $request) {
    return $this->app->handleRequest($request);
  }
  
}
