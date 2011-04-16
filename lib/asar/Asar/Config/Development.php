<?php
/**
 * Development configuration. Sets some request and response filters useful
 * during development. This is set when config 'mode' is set to 'development'.
 * See the FDebuggingExample functional test for ideas on usage.
 *
 * @package Asar
 * @subpackage core
 */
class Asar_Config_Development extends Asar_Config {
  
  protected $config = array(
    'request_filters'          => array(
      'standard' => 'Asar_MessageFilter_Standard',
      'development' => 'Asar_MessageFilter_Development'
    ),
    'response_filters'          => array(
      'development' => 'Asar_MessageFilter_Development',
      'standard'    =>    'Asar_MessageFilter_Standard',
    ),
  );
  
}
