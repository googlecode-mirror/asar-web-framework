<?php

require_once 'PHPUnit/Framework.php';
 
class AllTest extends PHPUnit_Framework_TestCase {
  
  function testYay() {
    $this->assertTrue(False, 'NOOOO');
  }
}

?>