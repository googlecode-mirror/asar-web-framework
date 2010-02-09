<?php
class Asar_Template_Builder {
  
  private $resource;
  
  function __construct($resource) {
    $this->resource = $resource;
  }
  
  static function getBuilder(Asar_Resource $resource) {
    return new self($resource);
  }
  
  function getTemplate($method, $content_type) {
    $tpl = new Asar_Template;
    $tpl->setTemplateFile(
      $this->_getTemplateFile($method, $content_type)
    );
    return $tpl;
  }
  
  private function _getPrefix() {
    $cname = get_class($this->resource);
    $start = strpos($cname, 'Resource_') + 9;
    $prefix = str_replace('_', '/', substr($cname, $start));
    return $prefix;
  }
  
  function _getTemplateFile($method, $content_type) {
    $config = $this->resource->getConfiguration();
    $path = Asar::constructPath(
      $config['default_representation_dir'],
      $this->_getPrefix() . ".$method.$content_type.php"
    );
    if (file_exists($path)) {
      return $path;
    }
    $path = Asar::constructPath(
      $config['default_representation_dir'],
      $this->_getPrefix(), "$method.$content_type.php"
    );
    if (!file_exists($path)) {
      throw new Asar_Template_Builder_Exception(
        'Unable to build template for ' . get_class($this->resource) . ' with '.
        "$method $content_type request."
      );
    }
    return $path;
  }
  
}
