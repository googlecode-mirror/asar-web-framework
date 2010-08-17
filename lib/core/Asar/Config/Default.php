<?php

class Asar_Config_Default extends Asar_Config {
  
  protected $config = array(
    'map'              => array('/' => 'Index'),
    'template_engines' => array('php' => 'Asar_Template'),
    'default_classes'  => array(
      'application'      => 'Asar_ApplicationBasic',
  		'config'           => 'Asar_Config_Default',
  		'status_messages'  => 'Asar_Response_StatusMessages',
    ),
  );
  
}
