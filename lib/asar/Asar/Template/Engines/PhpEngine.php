<?php
namespace Asar\Template\Engines;

use \Asar\Template\TemplateInterface;
use \Asar\Template\Exception\TemplateFileNotFound;
/**
 */
class PhpEngine implements TemplateInterface {
  
  
  private
    $file,
    $layout = array(),
    $css = array(),
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
  
  /**
   * @todo Is this even necessary?
   */ 
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
    return $this->insertDependents(ob_get_clean());
  }
  
  private function sortDependencies($dependencies) {
    $result = array();
    uasort($dependencies, function($a, $b) {
      if ($a['dependency'] == $b['file']) {
        return 1;
      }
      if ($b['dependency'] == $a['file']) {
        return -1;
      }
      if (is_null($a['dependency'])) {
        -1;
      }
      if (is_null($b['dependency'])) {
        1;
      }
      return 0;
    });
    return $dependencies;
  }
  
  private function insertDependents($output) {
    $head = '';
    $result = $this->sortDependencies($this->css);
    foreach ($result as $key => $values) {
      $head .= "{$values['style']}\n";
    }
    if ($head) {
      return str_replace('</head>', "\n$head</head>", $output);
    }
    return $output;
  }
  
  function includeJs() {
    
  }
  
  function includeCss($file, $dependency = null) {
    $this->css[$file] = array(
      'file' => $file,
      'style' => '<link rel="stylesheet" type="text/css" media="screen" ' .
        "src=\"/$file\" />",
      'dependency' => $dependency
    );
  }
  
}
