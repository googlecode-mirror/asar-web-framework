<?php

class Asar_TemplatePackageProvider {
  
  private $locator, $factory;
  
  function __construct(
    Asar_TemplateLocator $locator,
    Asar_TemplateFactory $factory
  ) {
    $this->locator = $locator;
    $this->factory = $factory;
  }
  
  function getTemplatesFor($resource_name, Asar_Request_Interface $request) {
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
