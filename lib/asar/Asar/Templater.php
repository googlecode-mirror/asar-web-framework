<?php
namespace Asar;

use \Asar\Resource\ResourceInterface;
use \Asar\Response\ResponseInterface;
use \Asar\Request\RequestInterface;
use \Asar\Config\ConfigInterface;
use \Asar\Config;
use \Asar\PathDiscover\PathDiscoverInterface;
use \Asar\TemplateRenderer;
/**
 * @todo remove "as ..."
 */
use \Asar\Templater\Exception as TemplaterException;
/**
 * @package Asar
 * @subpackage core
 */
class Templater
  implements ResourceInterface, ConfigInterface, PathDiscoverInterface
{
  
  private $resource, $renderer, $config;
  
  function __construct(
    ResourceInterface $resource,
    TemplateRenderer $renderer
  ) {
    $this->resource = $resource;
    if ($this->resource instanceof ConfigInterface) {
      $this->config = $resource;
    } else {
      $this->config = new Config;
    }
    $this->renderer = $renderer;
  }
  
  function handleRequest(RequestInterface $request) {
    $response = $this->resource->handleRequest($request);
    if (!$response instanceof ResponseInterface) {
      throw new TemplaterException(
        'Unable to create template. The Resource did not return a response ' .
        'object.'
      );
    }
    if ($this->responseTemplatable($response)) {
      $response = $this->renderer->renderFor(
        get_class($this->resource), $response, $request
      );
    }
    return $response;
  }
  
  private function responseTemplatable($response) {
    return $this->resource->getConfig('use_templates') && 
    $response->getStatus() == 200;
  }
  
  function getConfig($key = null) {
    return $this->config->getConfig($key);
  }
  
  function importConfig(ConfigInterface $config) {
    return $this->config->importConfig($config);
  }
  
  function getPermaPath($path_params = array()) {
    return $this->resource->getPermaPath($path_params);
  }
  
}
