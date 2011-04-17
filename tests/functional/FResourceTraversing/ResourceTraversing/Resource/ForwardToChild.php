<?php

class ResourceTraversing_Resource_ForwardToChild extends \Asar\Resource {
  
  function GET() {
    $this->forwardTo('Parent_Child');
  }
    
}

