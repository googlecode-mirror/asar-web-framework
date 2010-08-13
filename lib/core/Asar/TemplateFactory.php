<?php

class Asar_TemplateFactory {
  
  private $engines;
  
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
        return $template;
      }
    }
    return null;
  }
}
