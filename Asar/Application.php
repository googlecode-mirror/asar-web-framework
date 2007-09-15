<?php
require_once 'Asar.php';

abstract class Asar_Application extends Asar_Base implements Asar_Requestable {
  protected $router;
  /*
  private static $instance = NULL;
  
  static function instance() {
    if (self::$instance == NULL ) {
 			self::$instance = new self();
    }
    return self::$instance;
  }
  
  private function __construct() {}
  
  private function __clone() {}
  */
  
  function __construct() {
    $router_class_name = $this->getAppName().'_Router';
    $this->router = Asar::instantiate($router_class_name);
  }
  
  function processRequest(Asar_Request $request, array $arguments = NULL) {
    $response = $request->sendTo($this->router, $arguments);
    if (!($response instanceof Asar_Response)) {
      $this->exception('There was an error processing the request. The returned value must be a valid Asar_Response object');
      return NULL;
    } else {
      return $response;
    }
  }
  
  protected function getAppName() {
    return str_replace('_Application', '', get_class($this));
  }
  
  protected function loadClassResource($type, $name) {
    return Asar::loadClass($this->getAppName().'_'.$type.'_'.$name);
  }
  
  function loadController($name) {
    return $this->loadClassResource('Controller', $this->camelCase($name));
  }
  
  function loadModel($name) {
    return $this->loadClassResource('Model', $this->camelCase($name));
  }
  
  function loadFilter($name) {
    return $this->loadClassResource('Filter', $this->camelCase($name));
  }
  
  function loadHelper($name) {
    return $this->loadClassResource('Helper', $this->camelCase($name));
  }
  
  function loadView($controller, $action = '') {
    $view = $this->getAppName().'/View/'.$controller;
    if ($action !== '') {
      $view .= '/'.$action;
    }
    return $view.'.php';
  }
}

class Asar_Application_Exception extends Asar_Base_Exception {}

?>