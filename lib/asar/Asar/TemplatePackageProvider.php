<?php
namespace Asar;

use \Asar\Request\RequestInterface;
/**
 */
class TemplatePackageProvider {
  
  private $locator, $factory;
  
  function __construct(
    TemplateLocator $locator,
    TemplateFactory $factory
  ) {
    $this->locator = $locator;
    $this->factory = $factory;
  }
  
  function getTemplatesFor($resource_name, RequestInterface $request) {
    $tpl_file = $this->locator->locateFor($resource_name, $request);
    return array(
      'template'  => $this->factory->createTemplate($tpl_file),
      'mime-type' => $this->locator->getMimeTypeFor($tpl_file),
      'layout'    => $this->factory->createTemplate(
        $this->locator->locateLayoutFor($tpl_file)
      )
    );
  }
  
}
