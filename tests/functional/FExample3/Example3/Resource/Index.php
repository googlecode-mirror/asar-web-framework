<?php
class Example3_Resource_Index extends Asar_Resource
{
  public function GET()
  {
    $this->template->p  = 'This is the paragraph. Easy, no?';
  }
  
  public function POST()
  {
    $this->template->h2 = 'This is the subheading for the POST template';
    $this->template->p  = 'And this is the paragraph';
  }
}

