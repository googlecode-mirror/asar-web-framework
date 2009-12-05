<?php
class Example3_Resource_Alternative extends Asar_Resource {
  
  public function GET() {
    $this->template->p  = 'This is an alternative template setup.';
  }
  
}
