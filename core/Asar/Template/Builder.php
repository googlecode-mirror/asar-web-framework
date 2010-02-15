<?php
class Asar_Template_Builder {
  
  private $resource;
  private $rconf = null;
  private $engine = 'Asar_Template';
  
  function __construct($resource) {
    $this->resource = $resource;
    $this->rconf = $this->resource->getConfiguration();
  }
  
  static function getBuilder(Asar_Resource $resource) {
    return new self($resource);
  }
  
  function getTemplate($method, $content_type) {
    $tpl = new $this->engine;
    var_dump($tpl);
    $tpl->setTemplateFile(
      $this->_getTemplateFile($method, $content_type, $tpl->getTemplateFileExtension())
    );
    $layout_file = $this->_constructTemplateFilePath(
      'Layout.'.$content_type.'.'. $tpl->getTemplateFileExtension()
    );
    if ($layout_file) {
      $tpl->setLayoutFile($layout_file);
    }
    return $tpl;
  }
  
  function setEngine($engine) {
    echo "\nSetting Engine... $engine";
    $this->engine;
  }
  
  private function _getPrefix() {
    $cname = get_class($this->resource);
    $start = strpos($cname, 'Resource_') + 9;
    $prefix = str_replace('_', DIRECTORY_SEPARATOR, substr($cname, $start));
    return $prefix;
  }
  
  private function _getTemplateFile($method, $content_type, $extension) {
    $suffix = "$method.$content_type.$extension";
    echo "\nFinding: $suffix";
    $path = $this->_constructTemplateFilePath($this->_getPrefix() . ".$suffix");
    if (!$path) {
      $path = $this->_constructTemplateFilePath(
        Asar::constructPath($this->_getPrefix(), $suffix)
      );
    }
    if ($path) {
      return $path;
    }
    throw new Asar_Template_Builder_Exception(
      'Unable to build template for ' . get_class($this->resource) . ' with '.
      "$method $content_type request."
    );
  }
  
  private function _constructTemplateFilePath($suffix) {
    $file = Asar::constructPath(
      $this->rconf['default_representation_dir'], $suffix 
    );
    return file_exists($file) ? $file : false;
  }
  
}
