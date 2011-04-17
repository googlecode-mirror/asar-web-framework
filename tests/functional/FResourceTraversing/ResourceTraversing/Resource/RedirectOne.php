<?php

class ResourceTraversing_Resource_RedirectOne extends \Asar\Resource {
  
  function GET() {
    $this->redirectTo('Parent_Child_GrandChild');
  }
    
}
