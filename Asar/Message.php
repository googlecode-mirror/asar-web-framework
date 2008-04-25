<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

abstract class Asar_Message extends Asar_Base {
	private $address    = null;
	private $contents   = null;
	private $params     = array();
	private $type       = null;
	private $headers    = array();
	private $context    = null;
	private static $mime_types = array(
		'html'   => 'text/html',
		'htm'    => 'text/html',
		'php'    => 'text/html',
		'rss'    => 'application/xml',
		'xml'    => 'application/xml',
		'xhtml'  => 'application/xhtml+xml',
		'txt'    => 'text/plain',
		'xhr'    => 'text/plain',
		'css'    => 'text/css',
		'js'     => 'text/javascript',
		'json'   => 'application/json',
		);

	static function getSupportedTypes()
	{
	   return self::$mime_types;
	}
	
	function setHeaders($headers) {
		if (is_array($headers)) {
			$this->headers = $headers;
		}
	}

	function getHeaders() {
		if (!(count($this->headers) > 0)) {
			$this->exception('Headers not set');
		} else {
			return $this->headers;
		}
	}

	function setAddress($address) {
		$this->address = $address;
	}  

	function getAddress() {
		if (is_null($this->address)) {
			$this->exception('Address not set');
		} else {
			return $this->address;
		}
	}

	function setContent($contents) {
		$this->contents = $contents;
	}

	function getContent() {
		return $this->contents;
	}

	// For query strings
	function setParams($params) {
		if (is_array($params)) {
			$this->params = array_merge($this->params, $params);
		} else {
			$this->exception('Params must be an associative array');
		}
	}

	function getParams() {
		return $this->params;
	}

	function setParam($key, $value) {
		$this->params[$key] = $value;
	}

	function getParam($key) {
		if (array_key_exists($key, $this->params)) {
			return $this->params[$key];
		} else {
			$this->exception("The parameter '$key' specified does not exist");
		}
	}


	// @todo: Better mime-type setting ('text/plain', 'text/html', etc.)
	function setType($type) {
		$this->type = $type;
	}

	function getType() {
		return $this->type;
	}

	function getMimeType() {
		/**
		 * @todo needs improvement to skip with the @
		 */
		if (@ array_key_exists($this->type, self::$mime_types)) {
			return self::$mime_types[$this->type];
		} else {
			return 'text/html';
		}
	}

	protected function setContext($processor) {
		$this->context = $processor;
	}

	function getContext() {
		return $this->context;
	}

	function __toString() {
		if (is_array($this->contents)) {
			return implode("\n", $this->contents);
		}
		if (is_null($this->contents)) {
			return '';
		}
		return $this->contents;
	}
  
}

class Asar_Message_Exception extends Asar_Base_Exception {}
