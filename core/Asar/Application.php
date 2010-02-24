<?php

class Asar_Application implements Asar_Requestable {

  private $map = array();
  private $app_prefix = null;
  private $router;
  protected $config = array();
  
  function __construct(Asar_Resource_Router $router) {
    $this->setRouter($router);
    $this->setUp();
  }
  
  protected function setUp(){ }
  
  function setRouter(Asar_Resource_Router $router) {
    $this->router = $router;
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $resource = $this->getResource($request);
    // send a 404 response when no resource is found
    if (!$resource) {
      $response = new Asar_Response;
      $response->setStatus(404);
    } elseif ($resource instanceof Asar_Requestable) {
      if (Asar::getMode() == Asar::MODE_DEBUG) {
        Asar::debug('Resource', get_class($resource));
      }
      $this->configureResource($resource);
      
      $response = $resource->handleRequest($request);
      if (!($response instanceof Asar_Response)) {
        //TODO: raise exception here!
        $response = new Asar_Response;
        $response->setStatus(500);
        $response->setContent(
          '\'' . get_class($resource) . '\' did not return a response object.'
        );
      }
    }
    $this->setResponseMsgForStatus($response, $request);
    return $response;
  }
  
  private function configureResource($resource) {
    if (method_exists($resource, 'setConfiguration')) {
      $config = array('context' => $this );
      if (array_key_exists('default_representation_dir', $this->config)) {
        $config['default_representation_dir'] = 
          $this->config['default_representation_dir'];
      }
      $resource->setConfiguration($config);
    }
  }
  
  private function getResource($request) {
    $r = null;
    if (array_key_exists($request->getPath(), $this->map)) {
      $r = $this->map[$request->getPath()];
    }
    // Pass to router if one is defined
    if (!$r) {
      try {
        $r = Asar::instantiate(
          $this->router->getRoute($this, $request->getPath()
        ));
      } catch (Exception $e) {}
    }
    return $r;
  }
  
  private function setResponseMsgForStatus($response, $request) {
    // TODO: See if there's a better way to do this:
    switch ($response->getStatus()) {
      case 404:
        $response->setContent(
          'File Not Found (404). ' .
          'Sorry, we were unable to find the resource you were looking for. '.
		        	'Please check that you got the address or URL correctly. If '.
		        	'that is the case, please email the administrator. Thank you '.
		        	'and please forgive the inconvenience.'
	      	);
        break;
      case 405:
        $response->setContent(
          'Method Not Allowed (405). ' .
          "The HTTP Method '{$request->getMethod()}' is not allowed for this resource."
        );
        break;
      case 406:
        $response->setContent(
          'Not Acceptable (406). ' .
          'An appropriate representation of the requested ' .
            'resource could not be found.'
        );
        break;
      case 500:
        $response->setContent(
          'Internal Server Error (500). ' .
          'The Server has encountered some problems. ' .
          'The resource returned: '. $response->getContent()
        );
        break;
    }
  }
  
  function setIndex($resource) {
    $this->setMap('/', $resource);
  }
  
  function setMap($key, $resource) {
    $this->map[$key] = $resource;
    /*
    if ($resource instanceof Asar_Requestable) {
      $this->map[$key] = $resource;
    } elseif (is_string($resource)) {
      try {
        $this->map[$key] = Asar::instantiate($resource);
      } catch(Asar_Exception $e) {
        $this->map[$key] = Asar::instantiate(
          $this->getResourceFullName($resource)
        );
      }
    }*/
  }
  
  function getMap($key) {
    return $this->map[$key];
  }
  
  private function getResourceFullName($name) {
    return $this->getApplicationPrefix() .
      '_Resource_' . $name;
  }
  
  function setAppPrefix($prefix) {
    $this->app_prefix = $prefix;
  }
  
  private function getApplicationPrefix() {
    if ($this->app_prefix) {
      return $this->app_prefix;
    } else {
		  $classname = get_class($this);
		  return substr($classname, 0, strrpos($classname, '_'));
	  }
  }
}
