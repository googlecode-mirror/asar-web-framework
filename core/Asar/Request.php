<?php
class Asar_Request implements Asar_Request_Interface
{
    private $path, $method, $content, $headers = array(), $params = array();
    
    public function setPath($path)
    {
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setMethod($method)
    {
        $this->method = $method;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function setHeader($field_name, $value)
    {
    	$this->headers[$this->dashCamelCase($field_name)] = $value;
    }
    
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->headers[$this->dashCamelCase($name)] = $value;
        }
    }
    
    // TODO: Move this to some other class?
    private function dashCamelCase($string)
    {
        return Asar_Utility_String::dashCamelCase($string);
    }
    
    public function getHeader($field_name)
    {
    	return $this->headers[$this->dashCamelCase($field_name)];
    }
    
    public function getHeaders()
    {
        return $this->headers;
    }
    
    public function setParams($params = array())
    {
        $this->params = $params;
    }
    
    public function getParams($params = array())
    {
        return $this->params;
    }
        
    
    public function __construct()
    {
        $this->method = 'GET';
        $this->path = '/';
        $this->headers['Accept'] = 'text/html';
    }
}
