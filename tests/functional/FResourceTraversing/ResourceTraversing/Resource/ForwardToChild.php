<?php

class ResourceTraversing_Resource_ForwardToChild extends Asar_Resource {
  
  function GET() {
    $this->forwardTo('Parent_Child');
  }
    
}

