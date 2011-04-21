<?php
class TemplatesExample_Resource_ContentNegotiation extends \Asar\Resource {
  
  public function GET() {
    return array('foo' => 'This is from ContentNegotiation.php');
  }
  
}
