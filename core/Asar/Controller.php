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
	protected $view       = NULL;    // Template object to use
  
	
	function handleRequest(Asar_Request $request, array $arguments = NULL) {
		$this->request = $request;
		$this->response = new Asar_Response;
		if (!$this->request->getType()) {
		    $this->request->setType('html');
		}
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
	
	function getContext()
	{
		return $this->context;
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
		$this->view = new Asar_Template_Html;
		$content = $this->{$this->request->getMethod()}();
		if (is_null($content)) {
			/**
			 * @todo Needs refactoring...
			 */
			if ($this->view->getTemplate()) {
				$template_file = $this->view->getTemplate();
				if (!Asar::fileExists($template_file)) {
					$template_file = $this->getViewPath() . 
						$template_file . '.' . $this->request->getType() . '.php';
					
				}
			} else {	
				$template_file = $this->getViewPath() . 
			                 $this->request->getMethod() .
			                 '.' . $this->request->getType() . '.php';
			}
			
			if (Asar::fileExists($template_file)) {
				$this->view->setController($this);
				$this->view->setTemplate($template_file);
				$layout_file = $this->getViewLayout();
				if ($this->request->getType() == 'html' && Asar::fileExists($layout_file)) {
                    $this->view->setLayout($layout_file);
                }
				$content = $this->view->fetch();
			} elseif (405 != $this->response->getStatus() && 'HEAD' != $this->request->getMethod() ) {
				$this->response->setStatus(406);
			}
		}
		if (is_null($this->response->getType())) {
			$this->response->setType($this->request->getType());
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
	 * Returns the path to view template that the controller is going to use
	 *
	 * @return string
	 **/
	private function getViewPath()
	{
		$classpath = explode('_', get_class($this));
		$classpath[1] = 'View';
		return implode('/', $classpath) . '/';
	}
	
	/**
	 * Returns the path to the layout file
	 *
	 * @return string
	 * @todo Maybe we can combine some methods here with getViewPath
	 **/
	private function getViewLayout()
	{
	    $classpath = explode('_', get_class($this));
	    return $classpath[0] . '/View/Layout.html.php';
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