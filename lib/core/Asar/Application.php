<?php

class Asar_Application implements Asar_Resource_Interface {
  
  private $router, $status_messages, $map;
  
  function __construct(
    $name, Asar_Router_Interface $router,
    Asar_Response_StatusMessages_Interface $status_messages,
    $map = array()
  ) {
    $this->name = $name;
    $this->router = $router;
    $this->status_messages = $status_messages;
    $this->map = $map;
    $this->setUp();
  }
  
  protected function setUp() {}
  
  protected function setIndex($resource_name) {
    $this->setMap('/', $resource_name);
  }
  
  protected function setMap($path, $resource_name) {
    $this->map[$path] = $resource_name;
  }
  
  function getMap() {
    return $this->map;
  }
  
  function getName() {
    return $this->name;
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $response = new Asar_Response;
    try {
      $resource = $this->router->route(
        $this->name, $request->getPath(), $this->getMap()
      );
      if (!$resource instanceof Asar_Resource_Interface) {
        throw new Exception(
          'Router did not return an Asar_Resource_Interface object.'
        );
      }
      $a_response = $resource->handleRequest($request);
      if ($a_response instanceof Asar_Response_Interface) {
        $response = $a_response;
      } else {
        throw new Exception(
          gettype($a_response) . "is not a valid response object."
        );
      }
    } catch (Asar_Router_Exception_ResourceNotFound $e) {
      $response->setStatus(404);
    } catch (Exception $e) {
      $response->setStatus(500);
      $response->setContent(
        $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine()
      );
    }
    $this->setResponseDefaults($response, $request);
    return $response;
  }
  
  private function setResponseDefaults($response, $request) {
    if ($this->status_messages) {
      $msg = $this->status_messages->getMessage($response, $request);
      if ($msg !== false) {
        $response->setContent($msg);
      }
    }
    if (!$response->getHeader('Content-Type')) {
      $response->setHeader('Content-Type', 'text/html');
    }
    if (!$response->getHeader('Content-Language')) {
      $response->setHeader('Content-Language', 'en');
    }
  }
  
}
