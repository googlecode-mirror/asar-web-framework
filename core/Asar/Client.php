<?php
class Asar_Client
{
    private $server;
    
    function GET($path, $params = array(), $headers = array()) {
        return $this->makeRequest('GET', $path, $params, null, $headers);
    }
    
    function POST($path, $content = array()) {
        return $this->makeRequest('POST', $path, array(), $content);
    }
    
    private function MakeRequest(
        $method, $path, $params = array(), $content = null, 
        $headers = array()
    )
    {
        $R = new Asar_Request;
        $R->setMethod($method);
        $R->setPath($path);
        $R->setParams($params);
        foreach ($headers as $key => $value) {
            $R->setHeader($key, $value);
        };
        $R->setContent($content);
        if ($this->server instanceof Asar_Requestable) {
            return $this->server->handleRequest($R);
        } else {
            $rstr = $this->createRawHttpRequestString($R);
            if ($rstr) {
                return $this->exportRawHttpResponse(
                    $this->sendRawHttpRequest($rstr)
                );
            }
        }
    }
    
    public function createRawHttpRequestString(Asar_Request_Interface $request)
    {
        $str = sprintf("%s %s HTTP/1.1\r\n", 
            $request->getMethod(), $request->getPath()
        );
        $headers = $request->getHeaders();
        $msg_body = '';
        $headers['Host'] = $this->getHostName();
        $headers['Connection'] = 'Close';
        if ($request->getMethod() == 'POST') {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $msg_body = $this->createParamStr($request->getContent());
            $headers['Content-Length'] = strlen($msg_body);
        }
        foreach ($headers as $key => $value) {
            $str .= $key . ': ' . $value . "\r\n";
        }
        return $str . "\r\n" . $msg_body;
    }
    
    public function getHostName() {
        return str_replace('http://', '', $this->getServer());
    }
    
    public function sendRawHttpRequest($request_str) {
        $fp = @fsockopen($this->getHostName(), 80, $errno, $errstr, 30);
        if ($fp === false)
            throw new Asar_Client_Exception(
                'Unable to connect to ' . $this->getHostName() . ':80.'
            );
        fwrite($fp, $request_str);
        $output = stream_get_contents($fp);
        fclose($fp);
        return $output;
    }
    
    public function exportRawHttpResponse($raw) {
        $R = new Asar_Response;
        $rawarr = explode("\r\n\r\n", $raw, 2);
        $R->setContent(array_pop($rawarr));
        $headers = explode("\r\n", $rawarr[0]);
        $response_line = array_shift($headers);
        $R->setStatus(intval(str_replace('HTTP/1.1 ', '', $response_line)));
        foreach($headers as $line) {
            $header = explode(':', $line, 2);
            $R->setHeader($header[0], ltrim($header[1]));
        }
        return $R;
    }
    
    private function createParamStr($params)
    {
        if (!is_array($params))
            return '';
        $post_pairs = array();
        foreach($params as $key => $value) {
            $post_pairs[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        return implode('&', $post_pairs);
    }
    
    function setServer($server) {
        if (is_string($server))
            $server = rtrim($server, '/');
        $this->server = $server;
    }
    
    function getServer() {
        return $this->server;
    }
}
