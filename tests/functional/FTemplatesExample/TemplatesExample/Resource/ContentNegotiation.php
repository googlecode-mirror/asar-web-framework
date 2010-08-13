<?php
class TemplatesExample_Resource_ContentNegotiation extends Asar_Resource {
  
  public function GET() {
    return array('foo' => 'This is from ContentNegotiation.php');
  }
  
}
