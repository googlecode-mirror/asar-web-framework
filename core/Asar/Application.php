<?php
require_once 'Asar.php';

abstract class Asar_Application extends Asar_Request_Handler {
    private $root_controller = null;
    
    function __construct() {
        $root_controller_class_name = $this->getAppName().'_Controller_Index';
        try {
            $this->root_controller = Asar::instantiate($root_controller_class_name);
        } catch (Asar_Exception $e) {
            
        }
        if (Asar::getMode() == Asar::MODE_DEVELOPMENT) {
            $this->response_filters[] = array('Asar_Filter_Common', 'filterResponse');
        }
        $this->request_filters[] = array('Asar_Filter_Common', 'filterRequestTypeNegotiation');
    }
    
    function processRequest(Asar_Request $request, array $arguments = NULL) {
        $time_start = microtime(true);
        if ($this->root_controller) {
            $response = $request->sendTo($this->root_controller, $arguments);
            if (!($response instanceof Asar_Response)) {
                $this->exception('There was an error processing the request. The returned value must be a valid Asar_Response object');
                return NULL;
            }
        } else {
            $response = new Asar_Response;
            $response->setStatus(404);
        }
		switch ($response->getStatus()) {
			case 404:
			case 405:
				$request->setContent($response);
				$response = $request->sendTo(new Asar_Controller_Default);
				break;
		}

        $this->debug('Execution Time', (microtime(true) - $time_start) . ' seconds');
        return $response;
        
    }
    
    protected function getAppName() {
        return Asar::getClassPrefix($this);
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
