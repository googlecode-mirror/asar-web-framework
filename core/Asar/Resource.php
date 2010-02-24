<?php
class Asar_Resource implements Asar_Requestable {
  private $request, $response, $template;
  private $config = array();
  protected $_types = array(
    'text/html'       => 'html',
    'application/xml' => 'xml',
    'text/plain'      => 'txt'
  );
  
  function setTemplate($template) {
    $this->template = $template;
  }
  
  function getTemplate() {
    return $this->template;
  }
  
  private function _runMethod($method) {
    return call_user_func(array($this, $method));
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $this->request = $request;
    $this->response = new Asar_Response;
    try {
      if ($request->getMethod() === 'POST') {
        $_POST = $this->request->getContent();
      }
      $this->response->setContent($this->_getContent($request));
      $this->_setResponseDefaults();
    } catch (Exception $e) {
      $this->response->setContent($e->getMessage());
      $this->response->setStatus(500);
    }
    return $this->response;
  }
  
  private function _setResponseDefaults() {
    if (!$this->response->getStatus()) {
      $this->response->setStatus(200);
    }
    if (!$this->response->getHeader('Content-Type')) {
      $this->response->setHeader('Content-Type', 'text/html');
    }
  }
  
  private function _getContent($request) {
    if ($this->_contentNegotiate($request->getHeader('Accept'))) {
      return $this->_runMethod($request->getMethod());
    }
    $this->response->setStatus(406);
  }
  
  protected function _contentNegotiate($accept) {
    if (array_key_exists($accept, $this->_types)) {
      return $accept;
    }
    $mime_types = array_keys($this->_types);
    foreach ($mime_types as $mime_type) {
      if (strpos($accept, $mime_type) !== FALSE) {
        return $mime_type;
      }
    }
    return null;
  }
  
  function GET() {
    $this->response->setStatus(405);
  }
  
  function POST() {
    $this->response->setStatus(405);
  }
  
  function PUT() {
    $this->response->setStatus(405);
  }
  
  function DELETE() {
    $this->response->setStatus(405);
  }
}
