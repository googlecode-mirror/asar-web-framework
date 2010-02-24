<?php
/* Resource dispatching */

//////////////////////// 1.
$app->setReceiver($resource);
$app->handleRequest($request);

// in handleRequest()
function handleRequest($request) {
  $response = $this->receiver->handleRequest($request);
  //...
}

// Or combined...
$app->letResourceHandleRequest($resource, $request);

function letResourceHandleRequest($resource, $request) {
  $response = $this->resource->handleRequest($request);
  //...
}



//////////////////////// 2. - Dispatcher

class Asar_Application implements Asar_Requestable {
  function __construct(Asar_Dispatcher $dispatcher) {
    $this->dispatcher = $dispatcher;
    $this->dispatcher->setConfiguration($this->getConfiguration());
    $this->dispatcher->setMapping($this->getMap());
  }
  
  function handleRequest($request) {
    $response = $this->dispatcher->dispatchFor($request, $resource);
    //...
  }
}

class Asar_Dispatcher {
  
  function dispatchFor($request, $resource) {
    
  }
}

$app = new Asar_Application($dispatcher);

