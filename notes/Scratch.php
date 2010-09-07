<?php

// CONFIG DEFAULTS:

array(
  'default_classes' => array(
  		'application'    => 'Asar_ApplicationBasic',
  		'config' => 'Asar_Config_Default'
  	),
  'templates' => array(
  		'engines'    => array('php' => 'Asar_Template'),
  		'use_layout' => true,
  	),
  'resource' => array(
  		'default_content_type' => 'text/html',
  		'default_language'     => 'en',
  		'use_templates'        => true,
  		'map'									 => array(
  			'/' => 'Index'
  		)
  	),
  'mime_types' => array(
  		'text/html'        => 'html',
      'text/plain'       => 'txt',
      'application/xml'  => 'xml',
      'application/json' => 'json',
  		'text/css'         => 'css',
  		'text/javascript'  => 'js'
  	),
  
);

