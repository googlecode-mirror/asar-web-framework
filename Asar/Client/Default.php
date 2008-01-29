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
	 **/
	public function sendRequestTo($request, Asar_Application $application)
	{
		$response = &parent::sendRequestTo($request, $application);
		echo $response;
		return $response;
	}
}

?>