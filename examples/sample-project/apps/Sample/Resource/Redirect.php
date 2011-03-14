<?php
class Sample_Resource_Redirect extends Asar_Resource {
  
  function GET() {
    $this->redirectTo('/');
  }
  
}