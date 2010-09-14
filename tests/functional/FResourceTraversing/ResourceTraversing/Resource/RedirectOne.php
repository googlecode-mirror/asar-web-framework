<?php

class ResourceTraversing_Resource_RedirectOne extends Asar_Resource {
  
  function GET() {
    $this->redirectTo('Parent_Child_GrandChild');
  }
    
}
