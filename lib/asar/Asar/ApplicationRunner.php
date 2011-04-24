<?php
namespace Asar;

use \Asar\Application;
use \Asar\Request\RequestInterface;

/**
 */
class ApplicationRunner {

  private $app;

  function __construct(Application $app) {
    $this->app = $app;
  }
  
  function run(RequestInterface $request) {
    return $this->app->handleRequest($request);
  }
  
}
