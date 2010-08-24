<?php

// TODO: Refactor this!!!
class Asar_ApplicationFactory {
  
  private $config;
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  private $file_searcher;
  
  function getApplication($app_name) {
    $classes = $this->getClasses($app_name);
    $app_full_name = $classes['app'];
    $app_config = new $classes['config'];
    $app_config->importConfig(new Asar_Config_Default);
    $sm = $app_config->getConfig('default_classes.status_messages');
    $template_factory = new Asar_TemplateFactory;
    $template_factory->registerTemplateEngine('php', 'Asar_Template');
    $router_class = $app_config->getConfig('default_classes.router');
    $app = new $app_full_name(
      $app_name,
      new $router_class(
        new Asar_ResourceFactory(
          new Asar_TemplateLFactory(
            new Asar_TemplateLocator(
              new Asar_ContentNegotiator,
              $this->getAppPath($app_name), array('php')
            ),
            $template_factory
          ),
          new Asar_TemplateSimpleRenderer,
          $app_config
        )
      ),
      new $sm,
      $app_config->getConfig('map')
    );
    return $app;
  }
  
  private function getAppPath($app_name) {
    if (!$this->file_searcher) {
      $this->file_searcher = new Asar_FileSearcher;
    }
    $app_path = $this->file_searcher->find($app_name);
    return $app_path;
  }
  
  private function getClasses($app_name) {
    $classes = array();
    $test = $app_name . '_Application';
    if (class_exists($test)) {
      $classes['app'] = $test;
    } else {
      $classes['app'] = $this->config->getConfig('default_classes.application');
    }
    $test = $app_name . '_Config';
    if (class_exists($test)) {
      $classes['config'] = $test;
    } else {
      $classes['config'] = $this->config->getConfig('default_classes.config');
    }
    return $classes;
  }
}
