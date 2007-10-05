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
		$this->params  = $this->request->getParams();
		$this->callActionSafelyFromArguments($arguments);
    // @todo: Make sure we reset the object's response for cleanup
		return $this->response;
	}
  
  protected function callActionSafelyFromArguments($arguments) {
    if ($arguments && array_key_exists('action', $arguments)) {
			$this->callAction($arguments['action']);
		} else {
			$this->callAction('index');
		}
  }
	
	protected function getReflection() {
		if (!$this->reflection) {
			$this->reflection = new ReflectionClass(get_class($this));
		}
		return $this->reflection;
	}
	
	protected function isActionExists($action) {
		if (!is_array($this->actions)) {
		  // Get all public methods (except processRequest) and store in actions list
			foreach ($this->getReflection()->getMethods() as $method) {
		  	if ($method->isPublic() && !$method->isStatic() && $method->getName() !== 'processRequest') {
		  		$this->actions[] = $method->getName();
		  	}
		  }
		}
		if (in_array($action, $this->actions)) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function callAction($action) {
		if ($this->isActionExists($action)) {
		  $this->$action();
		} else {
			// @todo: implement this in $this->exception
			throw new Asar_Controller_ActionNotFound_Exception("The action '$action' was not found in controller '". get_class($this)."'");
		}
	}
  
  protected function forwardTo($action_address) {
    $this->callActionSafelyFromArguments(array('action'=> $action_address));
  }
}

class Asar_Controller_Exception extends Asar_Base_Exception {}
class Asar_Controller_ActionNotFound_Exception extends Asar_Controller_Exception {}