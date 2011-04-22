<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample;

class Config extends \Asar\Config {

  protected $config = array(
    'template_engines' => array(
      'atpl' => 
      'Asar\Tests\Functional\TemplatesExample\TemplatesExample\AtplTemplateEngine'
    )
  );

}
