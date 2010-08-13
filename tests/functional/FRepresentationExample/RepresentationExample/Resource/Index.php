<?php

class RepresentationExample_Resource_Index extends Asar_Resource {
  
  function GET() {
    return array(
      'h1' => 'Hello World!',
      'p'  => 'This is the paragraph. Easy, no?'
    );
  }
}
