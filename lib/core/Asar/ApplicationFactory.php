<?php

class Asar_ApplicationFactory {
  
  private 
    $config,
    $file_searcher,
    $loaded_message_filters = array();
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function getApplication($app_name) {
    $app_scope = new Asar_ApplicationScope(
      $app_name, $this->config
    );
    return Asar_ApplicationInjector::injectApplication($app_scope);
  }
}
