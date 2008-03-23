<?php
require_once 'Asar.php';

abstract class Asar_Application extends Asar_Base implements Asar_Requestable {
  protected $root_controller_class_name;
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
    $root_controller_class_name = $this->getAppName().'_Controller_Index';
    $this->root_controller = Asar::instantiate($root_controller_class_name);
  }
  
  function processRequest(Asar_Request $request, array $arguments = NULL) {
    $response = $request->sendTo($this->root_controller, $arguments);
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
    return $this->loadClassResource('Controller', Asar::camelCase($name));
  }
  
  function loadModel($name) {
    return $this->loadClassResource('Model', Asar::camelCase($name));
  }
  
  function loadFilter($name) {
    return $this->loadClassResource('Filter', Asar::camelCase($name));
  }
  
  function loadHelper($name) {
    return $this->loadClassResource('Helper', Asar::camelCase($name));
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