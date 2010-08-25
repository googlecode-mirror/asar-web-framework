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

// Routing with prefix magic
// maps to app-name/{/[0-9]+/}/{/[0-12]+/}
class AppName_RtIntYowza_RtMnYeba {}
** class AppName_RtintYowza_RtmnYeba {}
class AppName_rtintYowza_rtmnYeba {}

