<?php
require_once 'Asar.php';

abstract class Asar_Controller extends Asar_Base implements Asar_Requestable {
	protected $response   = NULL; // Stores the current response object for the controller
	protected $request    = NULL; // The request object passed to controller
	protected $reflection = NULL; // This is used for class reflection
	protected $actions    = NULL; // A record for all the actions in the class;
	protected $params     = NULL; // Storage for the params from request object
	protected $map        = array(); // URI to Controller mappings
	protected $context    = NULL; // The object that called this controller
	protected $depth      = NULL;    // How deep is the controller on the path
	protected $path_array = array();
	protected $path       = NULL;  // The path of the controller
	protected $forward    = NULL;
  
	function __construct() {
		$this->response = new Asar_Response;
	}
	
	function processRequest(Asar_Request $request, array $arguments = NULL) {
		$this->request = $request;
		if ($arguments && array_key_exists('context', $arguments)) {
			$this->context = $arguments['context'];
			$this->depth = $this->context->getDepth() + 1;
		} else {
			$this->depth = 0;
		}
		
		$this->path_array = array_slice($this->request->getPathArray(), 0, $this->depth + 1);
		$this->path = str_replace('//', '/', implode('/', $this->path_array));
		
		//$this->params  = $this->request->getParams();
		if (!$this->route()) {
			$this->callResourceAction();
		}

		// @todo: Make sure we reset the object's response for cleanup
    	
		return $this->response;
	}
	
	/**
	 * Returns the subpath requested from request object
	 * if available. Returns false if there's none
	 *
	 * @return string or boolean false
	 **/
	private function nextPath()
	{
		$req_path = $this->request->getPathArray();
		if ($this->depth+1 < count($req_path)) {
			// Still have paths below
			return $req_path[$this->depth+1];
		} else {
			return false;
		}
	}
	
	private function callResourceAction() {
		$method = $this->request->getMethod();
		if ($method == Asar_Request::HEAD) {
			$method_name = 'GET';
		} else {
			$method_name = $this->getRequestMethodString();
		}
		if ($this->getReflection()->hasMethod($method_name)) { 
			if ($method != Asar_Request::HEAD) {
				$this->response->setContent($this->$method_name());
			}
		} else {
			$this->response->setStatus(405);
		}
	}
	
	/**
	 * Get how deep is the controller on the path
	 *
	 * @return int
	 **/
	function getDepth()
	{
		return $this->depth;
	}
	
	/**
	 * Returns the path from which
	 * this controller was invoked
	 *
	 * @return string
	 **/
	function getPath()
	{
		return $this->path;
	}	 	 	
	
	
	/**
	 * See if there are mapped resources for the given uri
	 *
	 * @return Asar_Response
	 **/
	private function route()
	{
		$next = $this->nextPath();
		if ($next) {
			if (array_key_exists($next, $this->map)) {
				// The path is mapped
				$controller = Asar::instantiate(Asar::getClassPrefix($this).'_Controller_'.$this->map[$next]);
				$this->response = $this->request->sendTo($controller, array('context'=>$this));
				return true;
			} elseif ($this->forward) {
				$controller = Asar::instantiate(Asar::getClassPrefix($this).'_Controller_'.$this->forward);
				$this->response = $this->request->sendTo($controller, array('context'=>$this));
				return true;
			} else {
				$this->response->setStatus(404);
				return true;
			}
		} else {
			return false;
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
			case Asar_Request::HEAD :
				return 'HEAD';
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