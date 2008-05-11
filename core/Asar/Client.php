<?php
require_once 'Asar.php';

class Asar_Client extends Asar_Base {
	
	private $name     = NULL;
	protected $request  = NULL;
	protected $response = NULL;
	
	function createRequest($arguments = NULL) {
		$req = new Asar_Request();
		if (is_array($arguments)) {
			$req->setUriAuthority($arguments['authority']);
			$req->setUriScheme($arguments['scheme']);
						
			if (array_key_exists('path', $arguments)) {
				$req->setPath($arguments['path']);
			} else {
				$req->setPath('/');
			}
			
			if (array_key_exists('method', $arguments)) {
				$req->setMethod($arguments['method']);
			}
			
			if (array_key_exists('headers', $arguments)) {
				$req->setHeaders($arguments['headers']);
			}
			
			if (array_key_exists('content', $arguments)) {
				$req->setContent($arguments['content']);
			}
			
			if (array_key_exists('params', $arguments)) {
				$req->setParams($arguments['params']);
			}
			
			if (array_key_exists('type', $arguments)) {
				$req->setType($arguments['type']);
			}
			if (array_key_exists('content', $arguments)) {
				$req->setContent($arguments['content']);
			}
		} else {
			$req->setPath('/');
		}
		$this->request = $req;
		return $req;
	}
	
	/**
	 * Returns the latest request created or NULL if none
	 *
	 * @return Asar_Request
	 **/
	function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * Sends request to the application specified
	 *
	 * @returns Asar_Response An response object
	 * @params mixed  The request. Can be an array for request arguments or an Asar_Request object itself
	 * @params Asar_Application The application that will receive the request
	 */
	function sendRequestTo(Asar_Application $application) {
		$this->response = $this->getRequest()->sendTo($application);
		return $this->response;
	}
	
	/**
	 * Retrieve response after request is sent to Application
	 *
	 * @returns Asar_Response An response object
	 * 
	 */
	function getResponse() {
		return $this->response;
	}
	
	function setName($name) {
		if (!is_string($name)) {
			$this->exception('Name passed to setName must be a string');
		} else {
			$this->name = $name;
		}
	}
	
	function getName() {
		if (is_null($this->name)) {
			$this->exception('No name for client was set');
		} else {
			return $this->name;
		}
	}
	
	
	
}

class Asar_Client_Exception extends Asar_Base_Exception {}
