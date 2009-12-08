<?php
class Asar_Response implements Asar_Response_Interface
{
    private $content = '';
    private $status;
    private $headers = array();
    
    function setContent($content) {
        $this->content = $content;
    }    
    
    function getContent() {
        return $this->content;
    }
    
    function setStatus($status) {
        $this->status = $status;
    }
    
    function getStatus() {
        return $this->status;
    }
    
    function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }
    
    function getHeader($key) {
        if (array_key_exists($key, $this->headers)) {
          return $this->headers[$key];
        }
        return null;
    }
    
    function setHeaders(array $headers){
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
    }
    
    function getHeaders(){
        return $this->headers;
    }
}

