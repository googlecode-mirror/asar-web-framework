<?php
/**
 * Asar_Controller class definition - Asar Web Framework Core
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to wayne@asartalo.org so we can send you a copy immediately.
 * 
 * @package   Asar-Core
 * @copyright Copyright (c) 2007-2008, Wayne Duran <wayne@asartalo.org>.
 * @since     0.1
 * @version   $Id$
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.google.com/p/asar-web-framework
 */

/**
 * Asar_Controller
 *
 * Asar_Controller is the basic request handler. In REST terms
 * you can think of Asar_Controller as the resource, although
 * this is not accurate.
 *
 * @package Asar-Core
 * @todo Write a better description
 * @todo How do we handle PUT request methods?
 * @todo How do we handle files uploaded?
 **/
abstract class Asar_Controller extends Asar_Base implements Asar_Requestable {
    protected $response   = null;    // Stores the current response object for the controller
	protected $request    = null;    // The request object passed to controller
	protected $params     = array(); // Storage for the params from request object
	protected $data       = array(); // Storage for the post contents of request object;
	protected $map        = array(); // URI to Controller mappings
	protected $context    = null;    // The object that called this controller or null if none
	protected $depth      = null;    // How deep is the controller on the path
	protected $path_array = array();
	protected $path       = null;    // The path of the controller
	protected $forward    = null;    // Controller to forward with the request when there are no mapped controllers/resources
	protected $view       = null;    // Template object to use
	protected $locator = null;
  
	
	function __construct() {
	    $this->initialize();
	}
	
	protected function initialize() {
	    
	}
	
	protected function setLocator($locator_name) {
	    $this->locator = call_user_func( array($locator_name, 'getLocator'), $this);
	}
	
	public function getLocator() {
	    return $this->locator;
	}
	
	function handleRequest(Asar_Request $request, array $arguments = null) {
		$this->request = $request;
		$this->response = new Asar_Response;
		// TODO: Move this out of the controller
		if (!$this->request->getType()) {
		    $this->request->setType('html');
		}
		if ($arguments && array_key_exists('context', $arguments)) {
			$this->context = $arguments['context'];
			$this->depth = $this->context->getDepth() + 1;
			
			if ($this->context instanceof Asar_Controller) {
			    $this->locator = $this->context->getLocator();
			}
			
		} else {
			$this->depth = 0;
		}
		
		$this->path_array = array_slice($this->request->getPathArray(), 0, $this->depth + 1);
		$this->path = str_replace('//', '/', implode('/', $this->path_array));
		
		$this->params  = $this->request->getParams();
		$this->data = $this->request->getContent();
		if (!$this->_route()) {
			$this->_callResourceAction();
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
	 * @todo Could use optimzation
	 **/
	private function _nextPath()
	{
		$req_path = $this->request->getPathArray();
		if ($this->depth+1 < count($req_path)) {
			// Still have paths below
			return $req_path[$this->depth+1];
		} else {
			return false;
		}
	}
	
	
	
	/**
	 * See if there are mapped resources for the given uri
	 *
	 * @return bool
	 **/
	private function _route()
	{
		// TODO: This is confusing. Refactor, man!
		$next = $this->_nextPath();
		if ($next) {
			if ($this->isResourceMapped($next)) {
				$controller = Asar::instantiate( $this->_getControllerName($this->map[$next]) );
				$this->response = $this->request->sendTo($controller, array('context'=>$this));
			} elseif ($this->forward) {
			    $controller = Asar::instantiate( $this->_getControllerName($this->forward) );
				$this->response = $this->request->sendTo($controller, array('context'=>$this));
			} else {
				$this->response->setStatus(404);
			}
			return true;
		} else {
			return false;
		}
	}
	
	private function _getControllerName($original_name) {
	    if ($this->locator) {
	        return $controller_name = $this->locator->find($original_name);
        } else {
            return $original_name;
        }
	}
	
	/**
	 * @todo Fix this logic
	 */
	private function _callResourceAction() {
		$this->view = new Asar_Template_Html;
		$content = $this->{$this->request->getMethod()}();
		if (is_null($this->response->getType())) {
			$this->response->setType($this->request->getType());
		}
		$this->response->setContent( $content );
		/*
		if (is_null($content)) {
			if ($this->view->getTemplate()) {
				$template_file = $this->view->getTemplate();
				if (!Asar::fileExists($template_file)) {
					$template_file = $this->_getViewPath() . 
						$template_file . '.' . $this->request->getType() . '.php';
					
				}
			} else {	
				$template_file = $this->_getViewPath() . 
			                 $this->request->getMethod() .
			                 '.' . $this->request->getType() . '.php';
			}
			
			if (Asar::fileExists($template_file)) {
				$this->view->setController($this);
				$this->view->setTemplate($template_file);
				$layout_file = $this->_getViewLayout();
				if ($this->request->getType() == 'html' && Asar::fileExists($layout_file)) {
                    $this->view->setLayout($layout_file);
                }
				$content = $this->view->fetch();
			} elseif (405 != $this->response->getStatus() && 'HEAD' != $this->request->getMethod() && 200 == $this->response->getStatus()) {
				$this->response->setStatus(406);
			}
		}*/
		
	}
	
	
	function url() {
		/**
		 * @todo Could use some optimization
		 */
		return $this->request->getUriScheme() . '://' . $this->request->getUriAuthority() . $this->path;
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
	 * Returns the path to view template that the controller is going to use
	 *
	 * @return string
	 **/
	private function _getViewPath()
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
	private function _getViewLayout()
	{
	    $classpath = explode('_', get_class($this));
	    return $classpath[0] . '/View/Layout.html.php';
	}
	
	
	/**
	 * See if the resource is mapped
	 *
	 * @return bool
	 * @param string resource the resource name
	 **/
	private function isResourceMapped($resource)
	{
		return array_key_exists($resource, $this->map);
	}
	
	
	
}
