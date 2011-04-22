<?php

namespace Asar\Tests\Functional\StatusCodesExample\StatusCodesExample;

class Application extends \Asar\Application {
  
  protected function setUp() {
    $this->setMap('/', 'Index');
    $this->setMap('/page', 'Page');
    $this->setMap('/a500', 'A500');
  }

}

