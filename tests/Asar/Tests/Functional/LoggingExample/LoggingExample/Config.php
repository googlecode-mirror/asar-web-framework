<?php

class LoggingExample_Config extends \Asar\Config {
  
  protected $config = array(
    'use_templates' => false,
  );
  
  function init() {
    $this->config['log_file'] = Asar::getInstance()
      ->getFrameworkTestsDataTempPath() . DIRECTORY_SEPARATOR . 'example.log';
  }
  
}
