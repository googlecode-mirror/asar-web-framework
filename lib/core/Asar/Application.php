<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_Application implements Asar_Resource_Interface {
  
  private
    $map,
    $router,
    $status_messages,
    $name,
    $request_filters = array(),
    $response_filters = array(),
    $forward_level_max = 20,
    $forward_recursion = 0;
  
  function __construct(
    $name, Asar_Router_Interface $router,
    Asar_Response_StatusMessages_Interface $status_messages,
    $map = array(),
    $request_filters = array(),
    $response_filters = array()
  ) {
    $this->name = $name;
    $this->router = $router;
    $this->status_messages = $status_messages;
    $this->map = $map;
    foreach ($request_filters as $filter) {
      $this->addRequestFilter($filter);
    }
    foreach ($response_filters as $filter) {
      $this->addResponseFilter($filter);
    }
    $this->setUp();
  }
  
  protected function setUp() {}
  
  protected function setIndex($resource_name) {
    $this->setMap('/', $resource_name);
  }
  
  protected function setMap($path, $resource_name) {
    $this->map[$path] = $resource_name;
  }
  
  protected function addRequestFilter(Asar_RequestFilter_Interface $filter) {
    $this->request_filters[] = $filter;
  }
  
  protected function addResponseFilter(Asar_ResponseFilter_Interface $filter) {
    $this->response_filters[] = $filter;
  }
  
  function getMap() {
    return $this->map;
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $response = new Asar_Response;
    $this->forward_recursion = 0;
    try {
      $request = $this->filterRequest($request);
      // TODO: Application shouldn't know about debug
      if ($request->getHeader('Asar-Internal-Debug')) {
        $request->getHeader('Asar-Internal-Debug')->set(
          'Application', $this->name
        );
      }
      $response = $this->filterResponse(
        $this->passRequest($request, $response, $request->getPath())
      );
    } catch (Exception $e) {
      $response->setStatus(500);
      $response->setContent($this->set500Message($e));
    }
    $this->setResponseDefaults($response, $request);
    return $response;
  }
  
  private function filterRequest($request) {
    foreach ($this->request_filters as $filter) {
      $request = $filter->filterRequest($request);
    }
    return $request;
  }
  
  private function filterResponse($response) {
    foreach ($this->response_filters as $filter) {
      $response = $filter->filterResponse($response);
    }
    return $response;
  }
  
  private function passRequest($request, $response, $path) {
    if ($this->forward_recursion >= $this->forward_level_max) {
      throw new Exception(
        "Maximum forwards reached for path '{$request->getPath()}'."
      );
    }
    $this->forward_recursion++;
    try {
      $resource = $this->router->route(
        $this->name, $path, $this->getMap()
      );
      $this->checkIfResource($resource);
      $response = $this->returnIfResponse($resource->handleRequest($request));
    } catch (Asar_Router_Exception_ResourceNotFound $e) {
      $response->setStatus(404);
    } catch (Asar_Resource_Exception_ForwardRequest $e) {
      $payload = $e->getPayload();
      $req = $payload['request'];
      $req->setHeader('Asar-Internal-Isforwarded', true);
      $response = $this->passRequest(
        $payload['request'], $response, $e->getMessage()
      );
    }
    if (
        $response->getStatus() >= 300 && 
        $response->getStatus() < 400 &&
        !Asar_Utility_String::startsWith($response->getHeader('Location'), '/')
      ) {
      $resource = $this->router->route(
        $this->name, $response->getHeader('Location'), $this->getMap()
      );
      if ($resource instanceof Asar_PathDiscover_Interface) {
        $response->setHeader('Location', $resource->getPermaPath());
      }
    }
    return $response;
  }
  
  private function set500Message($exception) {
    $trace = "\nTrace:";
    foreach ($exception->getTrace() as $tracepart) {
      if (!isset($tracepart['file'])) {
        continue;
      }
      $file = isset($tracepart['file']) ? $tracepart['file'] : '';
      $line = isset($tracepart['line']) ? $tracepart['line'] : '';
      $trace .= "\n  File: $file  - Line: $line";
    }
    return $exception->getMessage() . 
      "\nFile: " . $exception->getFile() . "\nLine: " . $exception->getLine() . 
      $trace;
  }
  
  private function checkIfResource($resource) {
    if (!$resource instanceof Asar_Resource_Interface) {
      throw new Exception(
        'Router did not return an Asar_Resource_Interface object.'
      );
    }
  }
  
  private function returnIfResponse($response) {
    if (!$response instanceof Asar_Response_Interface) {
      throw new Exception(
        gettype($response) . "is not a valid response object."
      );
    }
    return $response;
  }
  
  private function setResponseDefaults($response, $request) {
    $msg = $this->status_messages->getMessage($response, $request);
    if ($msg) {
      $response->setContent($msg);
    }
    if (!$response->getHeader('Content-Type')) {
      $response->setHeader('Content-Type', 'text/html');
    }
    if (!$response->getHeader('Content-Language')) {
      $response->setHeader('Content-Language', 'en');
    }
  }
  
}
