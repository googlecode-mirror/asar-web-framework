<?php

class ResourceTraversing_Resource_Blog_RtYear_RtMonth_RtTitle
  extends Asar_Resource
{
  
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
