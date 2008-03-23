<?php
/**
 * @todo: cookies and sessions
 */
require_once 'Asar.php';

class Asar_Request extends Asar_Message {
	private $path          = NULL;
	private $path_array    = array();
	private $method        = NULL;
	private $host          = NULL;
	private $uri_scheme    = NULL;
	private $uri_authority = NULL;

	const GET    = 'GET';
	const POST   = 'POST';
	const PUT    = 'PUT';
	const DELETE = 'DELETE';
	const HEAD   = 'HEAD';
  
  
	/**
	 * Sets the host
	 *
	 * @return void
	 * @param string $host 
	 **/
	function setHost($host)
	{
		$this->setUriAuthority($host);
	}
	
	
	/**
	 * Returns the host (e.g. www.example.com)
	 *
	 * @return string
	 **/
	function getHost()
	{
		return $this->getUriAuthority();
	}
	
	
	function setUri($uri) {
		// Get the scheme part first
		$this->uri_scheme = substr($uri, 0, strpos($uri, ':'));
		$path = str_replace($this->uri_scheme.'://', '', $uri);
		$this->uri_authority = substr($path, 0, strpos($path, '/'));
		$this->setPath( substr($path, strpos($path, '/'), strlen($path)+1) );
	}

	function getUri() {
		return $this->uri_scheme.'://'.$this->uri_authority.$this->path;
	}
	
	function setUriScheme($scheme) {
		$this->uri_scheme = $scheme;
	}
	
	function getUriScheme() {
		return $this->uri_scheme;
	}
	
	function setUriAuthority($autority) {
		$this->uri_authority = $autority;
	}
	
	function getUriAuthority() {
		return $this->uri_authority;
	}

	protected function getTypeFromPath($path) {
		// Remove the string after the '?'
		if (strpos($path, '?')) {
			$path = substr($path, 0, strpos($path,'?'));
		}

		// Remove the string before the last occurrence of the '/'
		$fname = substr($path, strrpos($path, '/') + 1);

		// Get the file extension
		return substr($fname, strrpos($fname, '.')+1);
	}
  
	function setPath($path) {
		if (strpos($path, '//') > -1) {
			$this->exception('The path specified has double slashes, \'//\', which is unresorvable ');
		}
		if (strpos($path, '/') !== 0) {
			$this->exception('The path must start with a single slash');
		}
		$this->path = ($path === '/') ? $path : rtrim($path, '/');
		$path_array = explode('/', $this->path);
		$path_array[0] ='/';
		$this->path_array = $path_array;
		
		$this->setType($this->getTypeFromPath($path));
	}
  
	function getPath() {
		return $this->path;
	}
  
	function getPathArray() {
		return $this->path_array;
	}
	
	function setMethod($method) {
		switch ($method) {
			case self::GET:
			case self::POST:
			case self::PUT:
			case self::DELETE:
			case self::HEAD:
				$this->method = $method;
				break;
			default:
				$this->exception('Unknown Request Method passed.');
		 }
	}

	
	/**
	 * Returns the method set for this request
	 * Defaults to 'GET' if no method was defined
	 * 
	 * @return string 
	 */
	function getMethod() {
		if (is_null($this->method)) {
			$this->method = self::GET;
		}
		return $this->method;
	}
  
  function setContent($contents) {
    if (is_array($contents)) {
      parent::setContent($contents);
    } else {
      $this->exception('Contents must be an associative array');
    }
  }
  
  function sendTo(Asar_Requestable $processor, array $arguments = NULL) {
    $this->setContext($processor);
    return $processor->processRequest($this, $arguments);
  }

  
}

class Asar_Request_Exception extends Asar_Message_Exception {}
