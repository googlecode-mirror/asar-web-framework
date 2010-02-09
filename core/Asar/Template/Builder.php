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
    $config = $this->resource->getConfiguration();
    $tpl->setTemplateFile(Asar::constructPath(
      $config['default_representation_dir'],
      $this->_getPrefix() . ".$method.$content_type.php"
    ));
    return $tpl;
  }
  
  private function _getPrefix() {
    $cname = get_class($this->resource);
    $start = strpos($cname, 'Resource_') + 9;
    $prefix = str_replace('_', '/', substr($cname, $start));
    return $prefix;
  }
  
}
