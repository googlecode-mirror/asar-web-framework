<?php

class Asar_Client_Default extends Asar_Client {
	
	function createRequest() {
		return parent::createRequest($_SERVER['REQUEST_URI'],
	    	array(
	    		'method' => $_SERVER['REQUEST_METHOD']
	    	)
		);
	}
}

?>