<?php

class LoggingExample_Resource_Index extends \Asar\Resource {
  
  function GET() {
    $logger = \Asar\Logger\Registry::getLogger($this);
    $logger->log('This is the first log.');
    $logger->log('This is the second log.');
    return 'Hello!';
  }
    
}

