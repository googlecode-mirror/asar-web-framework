<?php
namespace Asar;

use \Asar\Config\ConfigInterface;
use \Asar\ApplicationScope;
use \Asar\ApplicationInjector;

/**
 * @package Asar
 * @subpackage core
 */
class ApplicationFactory {
  
  private 
    $config;
  
  function __construct(ConfigInterface $config) {
    $this->config = $config;
  }
  
  function getApplication($app_name) {
    $app_scope = new ApplicationScope(
      $app_name, $this->config
    );
    return ApplicationInjector::injectApplication($app_scope);
  }
}
