<?php

class Asar_Config_Default extends Asar_Config {
  
  protected $config = array(
    'map'              => array('/' => 'Index'),
    'template_engines' => array('php' => 'Asar_Template_Engines_Php'),
    'use_templates'    => true,
    'default_classes'  => array(
      'application'     => 'Asar_ApplicationBasic',
      'config'          => 'Asar_Config_Default',
      'status_messages' => 'Asar_Response_StatusMessages',
      'router'          => 'Asar_Router_Default',
    ),
    'response_filters'          => array(
      'standard' => 'Asar_MessageFilter_Standard'
    ),
    'request_filters'          => array(
      'standard' => 'Asar_MessageFilter_Standard'
    ),
  );
  
}
