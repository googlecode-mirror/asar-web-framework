<?php
namespace Asar\Config;

use \Asar\Config;
/**
 * Development configuration. Sets some request and response filters useful
 * during development. This is set when config 'mode' is set to 'development'.
 * See the FDebuggingExample functional test for ideas on usage.
 */
class Development extends Config {
  
  protected $config = array(
    'request_filters'          => array(
      'standard'    => 'Asar\MessageFilter\Standard',
      'development' => 'Asar\MessageFilter\Development'
    ),
    'response_filters'          => array(
      'development' => 'Asar\MessageFilter\Development',
      'standard'    => 'Asar\MessageFilter\Standard',
    ),
  );
  
}
