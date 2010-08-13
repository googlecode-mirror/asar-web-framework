<?php

// TODO: Refactor this!!!
class Asar_ApplicationFactory {
  
  private $default_classes = array(
    'app'    => 'Asar_ApplicationBasic',
    'config' => 'Asar_Config_Default'
  );
  private $file_searcher;
  
  function getApplication($app_name) {
    $classes = $this->getClasses($app_name);
    $app_full_name = $classes['app'];
    $app_config    = new Asar_Config_Default(new $classes['config']);
    $config = $app_config->getConfig();
    $template_factory = new Asar_TemplateFactory;
    $template_factory->registerTemplateEngine('php', 'Asar_Template');
    $app = new $app_full_name(
      $app_name,
      new Asar_Router(
        new Asar_ResourceFactory(
          new Asar_TemplateLFactory(
            new Asar_TemplateLocator(
              new Asar_ContentNegotiator,
              $this->getAppPath($app_name), array('php')
            ),
            $template_factory
          ),
          new Asar_TemplateSimpleRenderer
        )
      ),
      new Asar_Response_StatusMessages,
      $config['map']
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
    $classes = $this->default_classes;
    $test = $app_name . '_Application';
    if (class_exists($test)) {
      $classes['app'] = $test;
    }
    $test = $app_name . '_Config';
    if (class_exists($test)) {
      $classes['config'] = $test;
    }
    return $classes;
  }
}
