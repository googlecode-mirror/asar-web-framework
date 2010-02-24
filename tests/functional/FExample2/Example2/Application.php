<?php

class Example2_Application extends Asar_Application {
  protected function setUp() {
    $this->setMap('/', 'Index');
    $this->setMap('/page', 'Page');
    $this->setMap('/500', '500');
  }
}

