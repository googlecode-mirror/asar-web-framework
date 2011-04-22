<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample\Resource;

class AltTemplate extends \Asar\Resource {
  
  function setUp() {
    // TODO: How do we set a different template engine?
    //$this->setConfig('template_engines.phaml', 'Asar_Template_Engines_Haml');
    //var_dump($this->config_bag);
    //$this->setTemplateEngine('haml');
  }
  
  public function GET() {
    return array('p'  => 'This is an alternative template setup.');
  }
  
}

