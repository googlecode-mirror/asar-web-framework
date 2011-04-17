<?php
class TemplatesExample_Resource_Index extends \Asar\Resource {
  
  public function GET() {
    return array('p' => 'This is the paragraph. Easy, no?');
  }
  
  public function POST() {
    return array(
      'h2' => 'This is the subheading for the POST template',
      'p'  => 'And this is the paragraph'
    );
  }
}

