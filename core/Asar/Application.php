<?php

class Asar_Application implements Asar_Requestable {

    private $map = array();
    private $app_prefix = null;
    private $router;
    protected $config = array();
    
    function __construct()
    {
        $this->initialize();
    }
    
    protected function initialize(){ }
    
    function setRouter(Asar_Resource_Router $router) {
      $this->router = $router;
    }
    
    function handleRequest(Asar_Request_Interface $request) {
        if (!$this->router) {
            $this->router = new Asar_Resource_Router;
        }
        $r = $this->map[$request->getPath()];
        
        // Pass to router if one is defined
        
        if (!$r) {
            try {
                $r = Asar::instantiate(
                  $this->router->getRoute($this, $request->getPath()
                ));
            } catch (Exception $e) {}
        }
        
        // send a 404 response when no resource is found
        if (!$r) {
            $response = new Asar_Response;
            $response->setStatus(404);
            $response->setContent(
                'File Not Found. ' .
                'Sorry, we were unable to find the resource you were looking for. '.
		          	'Please check that you got the address or URL correctly. If '.
		          	'that is the case, please email the administrator. Thank you '.
		          	'and please forgive the inconvenience.'
	        	);
            return $response;
        }
        if ($r instanceof Asar_Requestable) {
            if (method_exists($r, 'setConfiguration')) {
                $config = array(
                    'context' => $this
                );
                if ($this->config['default_representation_dir']) {
                    $config['default_representation_dir'] = 
                        $this->config['default_representation_dir'];
                }
                $r->setConfiguration($config);
            }
            $response = $r->handleRequest($request);
            
            // TODO: See if there's a better way to do this:
            if ($response instanceof Asar_Response) {
                $status = $response->getStatus();
                switch ($status) {
                    case 405:
                        $response->setContent(
                            'Method Not Allowed (405).' .
                            'The HTTP Method \'POST\' is not allowed for this resource.'
                        );
                        break;
                    case 406:
                        $response->setContent(
                            'Not Acceptable (406).' .
                            'An appropriate representation of the requested ' .
		                        'resource could not be found.'
                        );
                        break;
                    case 500:
                        $response->setContent(
                            'Internal Server Error (500)' .
                            'The Server has encountered some problems. ' .
                            'The resource returned: '. $response->getContent()
                        );
                        break;
                }
            } else {
                //TODO: raise exception here!
                return new Asar_Response;
            }
        }
        return $response;
    }
    
    function setIndex($resource) {
        $this->setMap('/', $resource);
    }
    
    function setMap($key, $resource) {
        if ($resource instanceof Asar_Requestable) {
            $this->map[$key] = $resource;
        } elseif (is_string($resource)) {
            try {
                $this->map[$key] = Asar::instantiate($resource);
            } catch(Asar_Exception $e) {
                $this->map[$key] = Asar::instantiate(
                    $this->getResourceFullName($resource)
                );
            }
        }
    }
    
    private function getResourceFullName($name) {
        return $this->getApplicationPrefix() .
            '_Resource_' . $name;
    }
    
    function setAppPrefix($prefix) {
        $this->app_prefix = $prefix;
    }
    
    private function getApplicationPrefix() {
        if ($this->app_prefix) {
            return $this->app_prefix;
        } else {
		    $classname = get_class($this);
		    return substr($classname, 0, strrpos($classname, '_'));
	    }
    }
}
