<?php
/**
 * @todo: cookies and sessions
 */
require_once 'Asar.php';

class Asar_Request extends Asar_Message {
  private $uri        = NULL;
  private $method     = NULL;
  
  const GET    = 1;
  const POST   = 2;
  const PUT    = 3;
  const DELETE = 4;
  const HEAD   = 5;
  
  
  function setUri($uri) {
    $this->uri = $uri;
    $this->setType($this->getTypeFromUri($uri));
  }
  
  protected function getTypeFromUri($uri) {
    // Remove the string after the '?'
    if (strpos($uri, '?')) {
      $uri = substr($uri, 0, strpos($uri,'?'));
    }
    
    // Remove the all string before the last occurrence of the '/'
    $fname = substr($uri, strrpos($uri, '/') + 1);
    
    // Get the file extension
    return substr($fname, strrpos($fname, '.')+1);
  }
  
  function getUri() {
    return $this->uri;
  }
  
  function setMethod($method) {
    switch ($method) {
      case self::GET:
      case self::POST:
      case self::PUT:
      case self::DELETE:
      case self::HEAD:
        $this->method = $method;
        return TRUE;
        break;
      default:
        $this->exception('Unknown Request Method passed.');
    }
  }
  
  function getMethod() {
    if (is_null($this->method)) {
      $this->exception('Method is not set');
    } else {
      return $this->method;
    }
  }
  
  function setContent($contents) {
    if (is_array($contents)) {
      parent::setContent($contents);
    } else {
      $this->exception('Contents must be an associative array');
    }
  }
  
  function sendTo(Asar_Requestable $processor, array $arguments = NULL) {
    $this->setContext($processor);
    return $processor->processRequest($this, $arguments);
  }
  
}

class Asar_Request_Exeption extends Asar_Message_Exception {}
?>
