<?php

class DebuggingExample_Resource_Index extends Asar_Resource {
  function GET() {
    return array(
      'h1' => 'Debugging Tests'
    );
    /*
     * TODO: Fix: This code introduces an error "Creating default object from empty value."
    $this->template->h1 = 'Debugging Tests';
    */
  }
}
