<?php
namespace Asar\MessageFilter;

use \Asar\RequestFilter\RequestFilterInterface;
use \Asar\ResponseFilter\ResponseFilterInterface;
use \Asar\Config\ConfigInterface;
use \Asar\Request\RequestInterface;
use \Asar\Response\ResponseInterface;
use \Asar\Message\MessageInterface;
use \Asar\Utility\String;

/**
 */
class Standard
  implements RequestFilterInterface, ResponseFilterInterface
{
  
  private $config;
  
  function __construct(ConfigInterface $config) {
    $this->config = $config;
  }
  
  function filterRequest(RequestInterface $request) {
    $this->removeInternalHeaders($request);
    return $request;
  }
  
  function filterResponse(ResponseInterface $response) {
    $this->reformatLocationHeader($response);
    $this->removeInternalHeaders($response);
    return $response;
  }
  
  private function reformatLocationHeader(ResponseInterface $response) {
    $location = $response->getHeader('Location');
    if ($location && !preg_match('/^http[s]?:\/\//', $location)) {
      $response->setHeader(
        'Location', 
        $this->config->getConfig('site_protocol') . '://' . 
        $this->config->getConfig('site_domain') . $location
      );
    }
  }
  
  private function removeInternalHeaders(MessageInterface $message) {
    $headers = $message->getHeaders();
    foreach (array_keys($headers) as $key) {
      if (String::startsWith($key, 'Asar-Internal')) {
        $message->unsetHeader($key);
      }
    }
  }
  
}
