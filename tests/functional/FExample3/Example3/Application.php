<?php

class Example3_Application extends Asar_Application {

  protected function setUp() {
    $this->setMap('/', 'Index');
    $this->setMap('/nolayout', 'Nolayout');
    $this->setMap('/set-layout', 'SetLayout');
    $this->setMap('/alternative', 'Alternative');
    $this->setMap('/xml', 'Xml');
  }
  
}

