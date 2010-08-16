<?php

// CONFIG DEFAULTS:

array(
  'default_classes' => array(
  		'app'    => 'Asar_ApplicationBasic',
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


array(
  'default_classes.app' => 'Asar_ApplicationBasic',
	'default_classes.config' => 'Asar_Config_Default',
  'templates.engines'    => array('php' => 'Asar_Template'),
  'templates.use_layout' => true,
  'resource.default_content_type' => 'text/html',
	'resource.default_language'     => 'en',
	'resource.use_templates'        => true,
	'resource.map'                  => array(
		'/' => 'Index'
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