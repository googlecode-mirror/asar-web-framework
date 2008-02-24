<?php
require_once 'Asar.php';

abstract class Asar_Controller extends Asar_Base implements Asar_Requestable {
	protected $response   = NULL;    // Stores the current response object for the controller
	protected $request    = NULL;    // The request object passed to controller
	protected $actions    = NULL;    // A record for all the actions in the class;
	protected $params     = NULL;    // Storage for the params from request object
	protected $map        = array(); // URI to Controller mappings
	protected $context    = NULL;    // The object that called this controller
	protected $depth      = NULL;    // How deep is the controller on the path
	protected $path_array = array();
	protected $path       = NULL;    // The path of the controller
	protected $forward    = NULL;    // Controller to forward with the request when there are no mapped controllers/resources
	protected $view       = array(); // Array of values to store on the view object ---> may need rethinking
  
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
		$content = $this->{$this->request->getMethod()}();
		if (is_null($content)) {
			$view_object = $this->getView();
			if ($view_object) {
				$view_object->setVars($this->view);
				$content = $view_object->fetch();
			}
		}
		$this->response->setContent( $content );
	}
	
	
	/**
	 * Default PUT method handler
	 *
	 * @return Asar_Response
	 **/
	function PUT()
	{
		$this->response->setStatus(405);
	}
	
	
	/**
	 * Default GET method handler
	 *
	 * @return Asar_Response
	 **/
	function GET()
	{
		$this->response->setStatus(405);
	}
	
	
	/**
	 * Default POST method handler
	 *
	 * @return Asar_Response
	 **/
	function POST()
	{
		$this->response->setStatus(405);
	}
	
	
	/**
	 * Default DELETE method handler
	 *
	 * @return Asar_Response
	 **/
	function DELETE()
	{
		$this->response->setStatus(405);
	}
	
	
	/**
	 * Default HEAD method handler
	 *
	 * @return Asar_Response
	 **/
	function HEAD()
	{
		$this->GET();
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
	 * @return bool
	 **/
	private function route()
	{
		$next = $this->nextPath();
		if ($next) {
			if ($this->isResourceMapped($next)) {
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
	
	/**
	 * Returns the view object that the controller is going to use
	 *
	 * @return Asar_Template
	 **/
	private function getView()
	{
		$classpath = explode('_', get_class($this));
		$classpath[1] = 'View';
		$template_file = implode('/', $classpath).'/'.$this->request->getMethod().'.php';
		if (Asar::fileExists($template_file)) {
			$template = new Asar_Template();
			$template->setTemplate($template_file);
			return $template;
		} else {
			return NULL;
		}
	}
	
	
	
	/**
	 * See if the resource is mapped
	 *
	 * @return bool
	 **/
	private function isResourceMapped($resource)
	{
		return array_key_exists($resource, $this->map);
	}
	
	
	
}

class Asar_Controller_Exception extends Asar_Base_Exception {}
class Asar_Controller_ActionNotFound_Exception extends Asar_Controller_Exception {}