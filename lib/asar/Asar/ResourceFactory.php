<?php
namespace Asar;

use \Asar\Config\ConfigInterface;
use Asar\Templater;
use Asar\TemplateRenderer;

/**
 * @package Asar
 * @subpackage core
 */
class ResourceFactory {
  
  function __construct(
    TemplatePackageProvider $tl_factory,
    TemplateSimpleRenderer $ts_renderer,
    ConfigInterface $config
  ) {
    $this->tl_factory = $tl_factory;
    $this->ts_renderer = $ts_renderer;
    $this->config = $config;
  }
  
  // TODO: This can be better designed by using delegation
  // The factory need only be passed the factories
  function getResource($resource_classname) {
    if (!class_exists($resource_classname)) {
      throw new ResourceFactory\Exception(
        "The resource class '$resource_classname' is not defined or could " .
        "not be found."
      );
    }
    $rep_classname = $this->getRepresentationClassName($resource_classname);
    //var_dump($resource_classname);exit;
    if (class_exists($rep_classname)) {
      $resource = new $rep_classname(new $resource_classname);
    } else {
      $resource = new Templater(
        new $resource_classname, 
        new TemplateRenderer(
          $this->tl_factory, $this->ts_renderer
        )
      );
    }
    if ($resource instanceof ConfigInterface) {
      $resource->importConfig($this->config);
    }
    return $resource;
  }
  
  function getRepresentationClassName($resource_classname) {
    $pos = strpos($resource_classname, '\Resource\\');
    return substr_replace(
      $resource_classname, '\Representation\\', $pos, strlen('\Resource\\')
    );
  }
  
}
