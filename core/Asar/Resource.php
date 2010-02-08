<?php
class Asar_Resource implements Asar_Requestable {
  private $request, $response, $template_engine;
  private $config = array();
  protected static $_types = array(
    'text/html'     => '.html',
    'application/xml' => '.xml',
    'text/plain'    => '.txt'
  );
  
  function setTemplateEngine($class) {
    $this->template_engine = $class;
  }
  
  function setConfiguration(array $config) {
    if (array_key_exists('context', $config)) {
      $this->config['default_representation_dir'] = 
        $this->createRepresentationDirectory($config['context']);
    }
    $this->config = array_merge($this->config, $config);
  }
  
  function getConfiguration() {
    return $this->config;
  }
  
  private function createRepresentationDirectory($context) {
    $reflector = new ReflectionClass(get_class($context));
    return dirname($reflector->getFileName()) . 
        DIRECTORY_SEPARATOR . 'Representation';
  }
  
  function __get($property) {
    if ($property == 'template') {
      if ( !isset($this->template_engine) ) {
        $this->setTemplateEngine('Asar_Template');
      }
      $this->template = new $this->template_engine;
      return $this->template;
    }
    trigger_error(
      "Unknown property '$property' via __get()",
      E_USER_WARNING
    );
  }
  
  private function _runMethod() {
    return call_user_func(array($this, $this->request->getMethod()));
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
    $content_type = $this->_contentNegotiate($request->getHeader('Accept'));
    if (!$content_type) {
      $this->response->setStatus(406);
    }
    $content = $this->_runMethod();
    if (!$content && isset($this->template)) {
      return $this->_renderTemplate($content_type);
    }
    return $content;
  }
  
  protected function _contentNegotiate($accept) {
    if (array_key_exists($accept, self::$_types)) {
      return $accept;
    }
    $mime_types = array_keys(self::$_types);
    foreach ($mime_types as $mime_type) {
      if (strpos($accept, $mime_type) !== FALSE) {
        return $mime_type;
      }
    }
    return null;
  }
  
  private function _renderTemplate($content_type) {
    if (!$this->template->getTemplateFile()) {
      // Infer template file based on name of context and this resource 
      $this->template->setTemplateFile(
        $this->_constructTemplateFilePath()
      );
    }
    if (array_key_exists('default_representation_dir', $this->config)) {
      $this->template->setLayout( Asar::constructPath(
        $this->config['default_representation_dir'],
        'Layout' . $this->_getTemplateTypeToUse() . '.php'
      ));
    }
    try {
      $content = $this->template->render();
      // TODO: Create more functional tests for content negotiation
      $this->response->setHeader( 'Content-Type', $content_type );
    } catch (Asar_Template_Exception_FileNotFound $e) {
      $this->response->setStatus(406);
      $content = '';
    }
    return $content;
  }
  
  protected function _constructTemplateFilePath() {
    if (array_key_exists('context', $this->config)) {
      $contextname = str_replace('_Application', '', get_class($this->config['context']));
      $thisname = str_replace( '_', DIRECTORY_SEPARATOR,
        substr_replace(get_class($this), '', 0, strlen($contextname) + 10)
      );
      $suffix = $this->request->getMethod() . $this->_getTemplateTypeToUse() . '.php';
      $path = Asar::constructPath(
      $this->config['default_representation_dir'],
        $thisname . '.' . $suffix
      );
      if (!file_exists($path)) {
        $path = Asar::constructPath(
          $this->config['default_representation_dir'], $thisname, $suffix
        );
      }
      return $path;
    }
    return false;
  }
  
  protected function _getTemplateTypeToUse() {
    $accept = $this->_contentNegotiate($this->request->getHeader('Accept'));
    if (array_key_exists($accept, self::$_types)) {
      return self::$_types[$accept];
    }
    return '.html';
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
