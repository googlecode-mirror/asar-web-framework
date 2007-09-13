<?php
require_once 'Asar.php';

abstract class Asar_Application extends Asar_Base implements Asar_Requestable {
  private $router;
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
    $this->router = Asar::instantiate($this->getAppName().'_Router');
  }
  
  function processRequest(Asar_Request $request, array $arguments) {
    // send to controller
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

?>