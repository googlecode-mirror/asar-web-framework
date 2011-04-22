<?php
namespace Asar;

use \Asar\Debug;

/**
 */
class TemplateFactory {
  
  private $engines, $debug;
  
  function __construct(Debug $debug = null) {
    $this->debug = $debug;
  }
  
  //TODO: This should be moved to constructor
  function registerTemplateEngine($extension, $engine) {
    $this->engines[$extension] = $engine;
  }
  
  function getRegisteredTemplateEngines() {
    return $this->engines;
  }
  
  function createTemplate($filename) {
    if (is_string($filename)) {
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      if (array_key_exists($extension, $this->engines)) {
        $engine = $this->engines[$extension];
        $template = new $engine;
        $template->setTemplateFile($filename);
        if ($this->debug) {
          if (!$this->debug->get('Templates')) {
            $this->debug->set('Templates', array());
          }
          $list_of_templates = $this->debug->get('Templates');
          $list_of_templates[] = $filename;
          $this->debug->set('Templates', $list_of_templates);
        }
        return $template;
      }
    }
    return null;
  }
}
