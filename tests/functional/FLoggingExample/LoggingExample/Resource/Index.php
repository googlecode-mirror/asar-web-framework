<?php

class LoggingExample_Resource_Index extends Asar_Resource {
  
  function GET() {
    $logger = Asar_Logger_Registry::getLogger($this);
    $logger->log('This is the first log.');
    $logger->log('This is the second log.');
    return 'Hello!';
  }
    
}

