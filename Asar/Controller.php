<?php
require_once 'Asar.php';

abstract class Asar_Controller extends Asar_Base implements Asar_Requestable {
  protected $response   = NULL; // Stores the current response object for the controller
  protected $request    = NULL; // The request object passed to controller
  protected $reflection = NULL; // This is used for class reflection
  protected $actions    = NULL; // A record for all the actions in the class;
  protected $params     = NULL; // Storage for the params from request object 
  
  function __construct() {
  	$this->response = new Asar_Response();
  }
	
	function processRequest(Asar_Request $request, array $arguments = NULL) {
		$this->request = $request;
		//$this->params  = $this->request->getParams();
		$this->callResourceAction();
    // @todo: Make sure we reset the object's response for cleanup
    	
		return $this->response;
	}
	
	private function callResourceAction() {
		if (!$this->request->getUri()) {
			$this->request->setUri('/');
		}
		if (array_key_exists($this->request->getUri(), $this->map)) {
			$method_name = $this->getRequestMethodString().'_'.$this->map[$this->request->getUri()];
			if ($this->getReflection()->hasMethod($method_name)) {
				$this->response->setContent($this->$method_name());
			} else {
				$this->response->setStatusCode(405);
			}
		} else {
			$this->response->setStatusCode(404);
		}
	}
	
	private function getRequestMethodString() {
		switch ($this->request->getMethod()) {
			case Asar_Request::GET :
				return 'GET';
			case Asar_Request::POST :
				return 'POST';
			case Asar_Request::PUT :
				return 'PUT';
			case Asar_Request::DELETE :
				return 'DELETE';
		}
	}
  
	protected function getReflection() {
		if (!$this->reflection) {
			$this->reflection = new ReflectionClass(get_class($this));
		}
		return $this->reflection;
	}
	
	
}

class Asar_Controller_Exception extends Asar_Base_Exception {}
class Asar_Controller_ActionNotFound_Exception extends Asar_Controller_Exception {}