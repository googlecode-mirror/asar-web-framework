<?php
class StatusCodesExample_Resource_Index extends \Asar\Resource {
  
  public function GET() {
    $this->setConfig('use_templates', false);
    return "This is a test.";
  }
}
