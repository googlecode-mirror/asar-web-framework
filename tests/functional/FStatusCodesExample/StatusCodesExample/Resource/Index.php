<?php
class StatusCodesExample_Resource_Index extends Asar_Resource {
  public function GET() {
    $this->config['use_templates'] = false;
    return "This is a test.";
  }
}

