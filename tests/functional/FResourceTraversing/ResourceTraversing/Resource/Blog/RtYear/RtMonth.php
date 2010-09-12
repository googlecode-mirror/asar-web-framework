<?php

class ResourceTraversing_Resource_Blog_RtYear_RtMonth extends Asar_Resource {
  
  function GET() {
    return $this->request->getPath() . ' GET.';
  }
  
  function qualify($path) {
    return 
      preg_match('/^[1-9][0-9]{3}$/' , $path['year']) &&
      preg_match('/^[1-0][0-9]{1}$/' , $path['month']);
  }
}

