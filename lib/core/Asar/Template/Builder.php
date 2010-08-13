<?php
class Asar_Template_Builder {
  
  private $resource;
  private $tpl_dir = '';
  private $engine = 'Asar_Template';
  private $templates = array();
  
  function __construct($resource) {
    $this->resource = $resource;
    $conf = $this->resource->getConfiguration();
    if (isset($conf['default_representation_dir'])) {
      $this->tpl_dir = $conf['default_representation_dir'];
    }
  }
  
  static function getBuilder(Asar_Resource $resource) {
    return new self($resource);
  }
  
  function getTemplate() {
    if (!isset($this->templates[get_class($this->resource)])) {
      $this->templates[get_class($this->resource)] = new $this->engine;
    }
    return $this->templates[get_class($this->resource)];
  }
  
  function setTemplate(Asar_Template_Interface $tpl) {
    $this->templates[get_class($this->resource)] = $tpl;
    $this->setEngine(get_class($tpl));
  }
  
  function prepareTemplate($method, $content_type) {
    $tpl = $this->templates[get_class($this->resource)];
    $tpl->setTemplateFile($this->_getTemplateFile(
        $method, $content_type, $tpl->getTemplateFileExtension()
    ));
    $layout_file = $this->_constructTemplateFilePath(
      'Layout.'.$content_type.'.'. $tpl->getTemplateFileExtension()
    );
    if ($layout_file) {
      $tpl->setLayoutFile($layout_file);
    }
  }
  
  function setEngine($engine) {
    $this->engine = $engine;
  }
  
  private function _getPrefix() {
    $cname = get_class($this->resource);
    $start = strpos($cname, 'Resource_') + 9;
    $prefix = str_replace('_', DIRECTORY_SEPARATOR, substr($cname, $start));
    return $prefix ? $prefix : 'Asar_Resource';
  }
  
  private function _getTemplateFile($method, $content_type, $extension) {
    $suffix = "$method.$content_type.$extension";
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
      $this->tpl_dir, $suffix 
    );
    return file_exists($file) ? $file : false;
  }
  
}
