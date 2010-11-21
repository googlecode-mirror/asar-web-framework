<?php

// TODO: Refactor this!!!
class Asar_ApplicationFactory {
  
  private 
    $config,
    $file_searcher,
    $loaded_message_filters = array();
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function getApplication($app_name) {
    $classes = $this->getClasses($app_name);
    $app_full_name = $classes['app'];
    $app_config = new $classes['config'];
    if ('development' == $app_config->getConfig('mode')) {
      $app_config->importConfig(new Asar_Config_Development);
    }
    $app_config->importConfig(new Asar_Config_Default);
    // Set the status code messages
    $sm = $app_config->getConfig('default_classes.status_messages');
    // Set the templating Engine
    $template_factory = new Asar_TemplateFactory;
    $template_factory->registerTemplateEngine('php', 'Asar_Template');
    // Get Router
    $router_class = $app_config->getConfig('default_classes.router');
    // Instantiate Filters
    $filter_factory = new Asar_MessageFilterFactory($app_config, new Asar_Debug);
    $request_filters = $this->getRequestFilters($app_config, $filter_factory);
    $response_filters = $this->getResponseFilters($app_config, $filter_factory);
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
      $request_filters,
      $response_filters
    );
    return $app;
  }
  
  private function getFileSearcher() {
    if (!$this->file_searcher) {
      $this->file_searcher = new Asar_FileSearcher;
    }
    return $this->file_searcher;
  }
  
  private function getRequestFilters(Asar_Config_Interface $config, Asar_MessageFilterFactory $filter_factory) {
    return $this->filterBuilder($config, $filter_factory, 'request_filters');
  }
  
  private function getResponseFilters(Asar_Config_Interface $config, Asar_MessageFilterFactory $filter_factory) {
    return $this->filterBuilder($config, $filter_factory, 'response_filters');
  }
  
  private function filterBuilder(Asar_Config_Interface $config, Asar_MessageFilterFactory $filter_factory, $type) {
    $filters = array();
    foreach ($config->getConfig($type) as $key => $filter) {
      if (!isset($this->loaded_filters[$filter])) {
        $filterobj = $filter_factory->getFilter($filter);
        $this->loaded_filters[$filter] = $filterobj;
      }
      $filters[$key] = $this->loaded_filters[$filter];
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
