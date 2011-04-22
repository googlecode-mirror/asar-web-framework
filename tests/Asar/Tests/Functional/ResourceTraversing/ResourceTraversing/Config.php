<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing;

class Config extends \Asar\Config {
  
  protected $config = array(
    'use_templates' => false,
    'site_domain'   => 'asar-test.local',
    'site_protocol' => 'http',
  );
  
}
