<?php

class DebuggingExample_Resource_Index extends Asar_Resource {
  function GET() {
    $this->template->h1 = 'Debugging Tests';
  }
}
