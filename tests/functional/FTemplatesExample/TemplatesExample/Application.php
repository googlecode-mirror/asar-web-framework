<?php

class TemplatesExample_Application extends \Asar\Application {

  protected function setUp() {
    $this->setMap('/', 'Index');
    $this->setMap('/nolayout', 'Nolayout');
    $this->setMap('/set-layout', 'SetLayout');
    $this->setMap('/alternative', 'Alternative');
    $this->setMap('/xml', 'Xml');
    $this->setMap('/alt-template', 'AltTemplate');
  }
  
}

