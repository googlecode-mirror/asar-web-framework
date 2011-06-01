<?php
namespace Asar;

use \Asar\Config\ConfigInterface;
use \Asar\ApplicationScope;
use \Asar\ApplicationInjector;

/**
 */
class ApplicationFactory {
  
  private 
    $config;
  
  function __construct(ConfigInterface $config) {
    $this->config = $config;
  }
  
  function getApplication($app_name) {
    $container = new ApplicationInjector(
      $app_name, $this->config
    );
    //return ApplicationInjector::injectApplication($app_scope);
    return $container->Application;
  }
}
