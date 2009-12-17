<?php
class WebSetupExample_Resource_Index extends Asar_Resource {

  function GET() {
    $this->template->heading = 'Hello World!';
    $this->template->intro  = 'Welcome to my website. Here\'s what I do:';
    $this->template->cando = array(
      'Eat fish.', 'Ride bike.', 'Cook.', 'Run', 'Jog'
    );
  }
}

