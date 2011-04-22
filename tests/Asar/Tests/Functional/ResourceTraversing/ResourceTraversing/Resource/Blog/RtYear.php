<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource\Blog;

class RtYear extends \Asar\Resource {
  
  function GET() {
    return $this->request->getPath() . ' GET.';
  }
  
  function qualify($path) {
    return preg_match('/^[1-9][0-9]{3}$/' , $path['year']);
  }
}

