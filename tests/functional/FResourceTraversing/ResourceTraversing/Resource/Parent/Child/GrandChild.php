<?php

class ResourceTraversing_Resource_Parent_Child_GrandChild extends Asar_Resource {
  
  function GET() {
    return $this->getPath() . ' GET.';
  }
    
}

