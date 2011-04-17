<?php
namespace Asar\Template\Engines;

use \Asar\Template\TemplateInterface;
use \Asar\Template\Exception\TemplateFileNotFound;
/**
 * @package Asar
 * @subpackage core
 */
class PhpEngine implements TemplateInterface {
  
  
  private
    $file,
    $layout = array(),
    $config = array(
      'no_layout' => false
    );
  
  function setTemplateFile($file) {
    if (!file_exists($file)) {
      throw new TemplateFileNotFound(
        "The file '$file' passed to the template does not exist."
      );
    }
    $this->file = $file;
  }
  
  function getTemplateFile() {
    return $this->file;
  }
  
  // TODO: Is this even necessary?
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
      $content = $vars;
    }
    ob_start();
    include($this->file);
    return ob_get_clean();
  }
  
}
