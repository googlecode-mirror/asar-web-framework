<?php

class StatusCodesExample_Application extends \Asar\Application {
  
  protected function setUp() {
    $this->setMap('/', 'Index');
    $this->setMap('/page', 'Page');
    $this->setMap('/500', '500');
  }

}

