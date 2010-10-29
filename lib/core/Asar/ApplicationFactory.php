<?php

// TODO: Refactor this!!!
class Asar_ApplicationFactory {
  
  private $config, $file_searcher;
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function getApplication($app_name) {
    $classes = $this->getClasses($app_name);
    $app_full_name = $classes['app'];
    $app_config = new $classes['config'];
    $app_config->importConfig(new Asar_Config_Default);
    $sm = $app_config->getConfig('default_classes.status_messages');
    $template_factory = new Asar_TemplateFactory;
    $template_factory->registerTemplateEngine('php', 'Asar_Template');
    $router_class = $app_config->getConfig('default_classes.router');
    // Instantiate Filters
    $filters = $this->getFilters($app_config);
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
        ),
        new Asar_ResourceLister($this->getFileSearcher())
      ),
      new $sm,
      $app_config->getConfig('map'),
      $filters
    );
    return $app;
  }
  
  private function getFileSearcher() {
    if (!$this->file_searcher) {
      $this->file_searcher = new Asar_FileSearcher;
    }
    return $this->file_searcher;
  }
  
  private function getFilters(Asar_Config_Interface $config) {
    $filters = array();
    foreach ($config->getConfig('filters') as $filter) {
      $filters[] = new $filter($config);
    }
    return $filters;
  }
  
  private function getAppPath($app_name) {
    $app_path = $this->getFileSearcher()->find($app_name);
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
