<?php

class Asar_Config_Default extends Asar_Config {
  
  protected $config = array(
    'status_messages'  => 'Asar_Response_StatusMessages',
    'map'              => array('/' => 'Index'),
    'template_engines' => array('php' => 'Asar_Template'),
  );
  
  function __construct(Asar_Config_Interface $config = null) {
    if ($config) {
      $this->config = array_merge($this->config, $config->getConfig());
    }
  }
  
}
