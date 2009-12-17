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
    //$trace = debug_backtrace();
    trigger_error(
      "Unknown property '$property' via __get()",
      E_USER_WARNING
    );
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $this->request = $request;
    $this->response = new Asar_Response;
    try {
      if ($request->getMethod() === 'POST') {
        $_POST = $this->request->getContent();
      }
      $content_type = $this->contentNegotiate($request->getHeader('Accept'));
      if (!array_key_exists($content_type, self::$_types)) {
        $this->response->setStatus(406);
      }
      $content = call_user_func(array($this, $request->getMethod()));
      if (!$content && isset($this->template)) {
        if (!$this->template->getTemplateFile()) {
          // Infer template file based on name of context and this resource 
          $this->template->setTemplateFile(
            $this->constructTemplateFilePath()
          );
          //echo $this->template;
        }
        if (array_key_exists('default_representation_dir', $this->config)) {
          $this->template->setLayout( Asar::constructPath(
            $this->config['default_representation_dir'],
            'Layout' . $this->getTemplateTypeToUse() . '.php'
          ));
        }
        try {
          $content = $this->template->render();
          // TODO: Create more functional tests for content negotiation
          $this->response->setHeader( 'Content-Type', $content_type );
        } catch (Asar_Template_Exception_FileNotFound $e) {
          $this->response->setStatus(406);
        }
      }
      $this->response->setContent($content);
      if (!$this->response->getStatus()) {
        $this->response->setStatus(200);
      }
      if (!$this->response->getHeader('Content-Type')) {
        $this->response->setHeader('Content-Type', 'text/html');
      }
    } catch (Exception $e) {
      $this->response->setContent($e->getMessage());
      $this->response->setStatus(500);
    }
    return $this->response;
  }
  
  function contentNegotiate($accept) {
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
  
  protected function constructTemplateFilePath() {
    if (array_key_exists('context', $this->config)) {
      $contextname = str_replace('_Application', '', get_class($this->config['context']));
      $thisname = str_replace( '_', DIRECTORY_SEPARATOR,
        substr_replace(get_class($this), '', 0, strlen($contextname) + 10)
      );
      $suffix = $this->request->getMethod() . $this->getTemplateTypeToUse() . '.php';
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
  
  protected function getTemplateTypeToUse() {
    $accept = $this->contentNegotiate($this->request->getHeader('Accept'));
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
