<?php

class Asar_ResourceFactory {
  
  function __construct(
    Asar_TemplateLFactory $tl_factory,
    Asar_TemplateSimpleRenderer $ts_renderer,
    Asar_Config_Interface $config
  ) {
    $this->tl_factory = $tl_factory;
    $this->ts_renderer = $ts_renderer;
    $this->config = $config;
  }
  
  // TODO: This can be better designed by using delegation
  // The factory need only be passed the factories
  function getResource($resource_classname) {
    $rep_classname = $this->getRepresentationClassName($resource_classname);
    if (class_exists($rep_classname)) {
      $resource = new $rep_classname(new $resource_classname);
    } else {
      $resource = new Asar_Templater(
        new $resource_classname, 
        new Asar_TemplateRenderer(
          $this->tl_factory, $this->ts_renderer
        )
      );
    }
    if ($resource instanceof Asar_Config_Interface) {
      $resource->importConfig($this->config);
    }
    return $resource;
  }
  
  function getRepresentationClassName($resource_classname) {
    $pos = strpos($resource_classname, '_Resource_');
    return substr_replace(
      $resource_classname, '_Representation_', $pos, strlen('_Resource_')
    );
  }
  
}
