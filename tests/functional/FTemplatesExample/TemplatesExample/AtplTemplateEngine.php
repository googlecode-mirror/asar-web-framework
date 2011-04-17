<?php

use \Asar\Template\TemplateInterface;

/**
 * TemplatesExample_AtplTemplateEngine
 *
 * This is a super simple template engine that uses a str_replace. This
 * is only used for demonstrating how an alternative template engine can
 * be used in your applications.
 *
 */
class TemplatesExample_AtplTemplateEngine implements TemplateInterface {

  private
    $file,
    $layout = array(),
    $config = array(
      'no_layout' => false
    );
  
  function setTemplateFile($file) {
    if (!file_exists($file)) {
      throw new \Asar\Template\Exception\FileNotFound(
        "The file '$file' passed to the template does not exist."
      );
    }
    $this->file = $file;
  }
  
  function getTemplateFile() {
    return $this->file;
  }
  
  function setConfig($key, $value) {}
  
  function getLayoutVars() {
    return $this->layout;
  }
  
  function getConfig($key) {
    if (array_key_exists($key, $this->config)) {
      return $this->config[$key];
    }
  }
  
  function render($vars=array()) {
    if (is_array($vars)) {
      extract($vars);
    } else {
      $old_vars = $vars;
      $vars = array();
      $vars['content'] = $old_vars;
    }
    $output = file_get_contents($this->file);
    foreach ($vars as $key => $value) {
      $output = str_replace("[$key]", $value, $output);
    }
    return $output;
  }

}
