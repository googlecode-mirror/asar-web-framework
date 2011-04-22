<?php
namespace Asar\Config;

/**
 * Default configuration values
 */
class DefaultConfig extends \Asar\Config {
  
  protected $config = array(
    'map'              => array('/' => 'Index'),
    'template_engines' => array('php' => 'Asar\Template\Engines\PhpEngine'),
    'use_templates'    => true,
    'default_classes'  => array(
      'application'     => 'Asar\ApplicationBasic',
      'config'          => 'Asar\Config\DefaultConfig',
      'status_messages' => 'Asar\Response\StatusMessages',
      'router'          => 'Asar\Router\DefaultRouter',
    ),
    'response_filters'          => array(
      'standard' => 'Asar\MessageFilter\Standard'
    ),
    'request_filters'          => array(
      'standard' => 'Asar\MessageFilter\Standard'
    ),
  );
  
}
