<?php
class Asar_Template_Builder {
  
  private $resource;
  private $rconf = null;
  
  function __construct($resource) {
    $this->resource = $resource;
    $this->rconf = $this->resource->getConfiguration();
  }
  
  static function getBuilder(Asar_Resource $resource) {
    return new self($resource);
  }
  
  function getTemplate($method, $content_type) {
    $tpl = new Asar_Template;
    $tpl->setTemplateFile(
      $this->_getTemplateFile($method, $content_type)
    );
    $layout_file = $this->_constructTemplateFilePath('Layout.'.$content_type.'.php');
    if ($layout_file) {
      $tpl->setLayoutFile($layout_file);
    }
    return $tpl;
  }
  
  private function _getPrefix() {
    $cname = get_class($this->resource);
    $start = strpos($cname, 'Resource_') + 9;
    $prefix = str_replace('_', DIRECTORY_SEPARATOR, substr($cname, $start));
    return $prefix;
  }
  
  private function _getTemplateFile($method, $content_type) {
    $suffix = "$method.$content_type.php";
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
