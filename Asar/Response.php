<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

class Asar_Response extends Asar_Message {
  
  function sendTo(Asar_Respondable $processor) {
    $this->setContext($processor);
    $processor->processResponse($this);
  }
  
}

class Asar_Response_Exeption extends Asar_Message_Exception {}
?>
