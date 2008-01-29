<?php

class Asar_Client_Default extends Asar_Client {
	
	function createRequest() {
 		
		return parent::createRequest( $this->getUriFromServerVars(),
	    	array(
	    		'method' => $_SERVER['REQUEST_METHOD'],
				'params' => $_GET
	    	)
		);
	}
	
	/**
	 * Tries to obtain the uri from the $_SERVER global variable
	 *
	 * @return string URI 
	 **/
	private function getUriFromServerVars()
	{
		if (array_key_exists('REDIRECT_URL', $_SERVER)) {
 			return $_SERVER['REDIRECT_URL'];
 		} else {
			if (!array_key_exists('REQUEST_URI', $_SERVER)) {
				$this->exception('Unable to obtain path');
			}
 			
 			$qrstr_start = strpos($_SERVER['REQUEST_URI'], '?');
			if ($qrstr_start > 0) {
				return substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
			} else {
				return $_SERVER['REQUEST_URI'];
			}
		}
	}
	
	/**
	 * Sends request to Application and outputs the response to the buffer
	 *
	 * @return Asar_Response
	 * @param  Asar_Application
	 **/
	public function sendRequestTo(Asar_Application $application)
	{
		$response = parent::sendRequestTo($application);
		echo $response;
		return $response;
	}
}
class Asar_Client_Default_Exception extends Asar_Client_Exception {}

?>