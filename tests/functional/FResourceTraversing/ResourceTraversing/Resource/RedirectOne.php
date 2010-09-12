<?php

class ResourceTraversing_Resource_ForwardToChild extends Asar_Resource {
  
  function GET() {
    $this->redirectTo('Parent_Child_GrandChild');
  }
    
}
