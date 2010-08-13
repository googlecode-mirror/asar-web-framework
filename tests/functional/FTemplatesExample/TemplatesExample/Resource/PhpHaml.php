<?php
class TemplatesExample_Resource_PhpHaml extends Asar_Resource {
  
  function setUp() {
    // TODO: How do we set a different template engine?
    $this->setTemplateEngine('phphaml');
  }
  
  public function GET() {
    return array('p'  => 'This is an alternative template setup.');
  }
  
}

