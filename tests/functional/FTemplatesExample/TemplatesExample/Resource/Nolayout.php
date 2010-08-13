<?php
class TemplatesExample_Resource_Nolayout extends Asar_Resource {
  public function GET() {
    return array(
      'h1' => 'This is the main heading.',
      'p'  => 'This is the paragraph. Easy, no?'
    );
  }
}

