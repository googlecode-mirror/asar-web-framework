<?php

namespace Asar\Tests\Functional\LoggingExample\LoggingExample\Resource;

class Index extends \Asar\Resource {
  
  function GET() {
    /**
     * @todo this is overly complicated. The registration code should be
     * refactored and rethought.
     */
    $logger = \Asar\Logger\Registry::getLogger($this);
    $logger->log('This is the first log.');
    $logger->log('This is the second log.');
    return 'Hello!';
  }
    
}

