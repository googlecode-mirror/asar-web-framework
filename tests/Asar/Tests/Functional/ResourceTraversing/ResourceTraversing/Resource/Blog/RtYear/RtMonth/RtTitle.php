<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource\Blog\RtYear\RtMonth;

class RtTitle extends \Asar\Resource {
  
  function GET() {
    $path = $this->getPathComponents();
    return $path['title'];
  }
  
  function qualify($path) {
    return 
      preg_match('/^[1-9][0-9]{3}$/' , $path['year']) &&
      preg_match('/^[0-1][0-9]$/' , $path['month']);
  }
  
}

