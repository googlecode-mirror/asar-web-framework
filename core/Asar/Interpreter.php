<?php
class Asar_Interpreter implements Asar_Interprets {
    
    public function interpretFor(Asar_Requestable $requestable)
    {
        //TODO: How will this behave if $response is not an Asar_Response
        $response = $requestable->handleRequest($this->createRequest());
        $this->exportResponse($response);
    }
    
    public function createRequest()
    {
        $request = new Asar_Request;
        $request->setMethod($_SERVER['REQUEST_METHOD']);
        $request->setPath($this->createPathFromUri($_SERVER['REQUEST_URI']));
        foreach ($_SERVER as $key => $value) {
	        if (strpos($key, 'HTTP_') === 0) {
	            $request->setHeader(str_replace('HTTP_', '', $key), $value);
	        }
	    }
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            $request->setContent($_POST);
        return $request;
    }
    
    public function exportResponse(Asar_Response $response)
    {
        $this->exportResponseHeaders($response);
        echo $response->getContent();
    }
    
    public function exportResponseHeaders($response)
    {
        $headers = $response->getHeaders();
	    foreach ($headers as $name => $value)
	    {
	        $this->_header($name . ': ' . $value);
	    }
    }
    
    public function _header($header)
    {
        header($header);
    }
    
    private static function createPathFromUri($uri)
	{
	    $qrstr_start = strpos($uri, '?');
		if ($qrstr_start > 0)
			return substr($uri, 0, strpos($uri, '?'));
	    return $uri;
	}
}
