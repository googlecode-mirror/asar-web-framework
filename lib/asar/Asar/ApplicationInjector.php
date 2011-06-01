<?php
namespace Asar;

use \Asar\Config\ConfigInterface;

/**
 */
class ApplicationInjector extends \Pimple {

  function __construct($app_name, ConfigInterface $config) {
    $this->app_name = $app_name;
    $this->config   = $config;
    $this->defineGraph();
  }
  
  private function defineGraph() {
    $this->ApplicationRunner = function(\Pimple $c) {
      return new ApplicationRunner(
        $c->Application
      );
    };
    
    $this->Request = function(\Pimple $c) {
      return $c->getRequest();
    };
    
    $this->Application = function(\Pimple $c) {
      $app_full_name = $c->ApplicationClass;
      $c->registerLoggers;
      return new $app_full_name(
        $c->app_name,
        $c->Router,
        $c->StatusCodeMessages,
        $c->AppConfig->getConfig('map'),
        $c->RequestFilters,
        $c->ResponseFilters
      );
    };
    
    
    $this->registerLoggers = function(\Pimple $c) {
      if ($c->LogFile) {
        Logger\Registry::register(
          $c->app_name, $c->LogFile
        );
      }
    };
    
    $this->LogFile = function(\Pimple $c) {
      return $c->AppConfig->getConfig('log_file');
    };
    
    $this->Router = function(\Pimple $c) {
      $router_class = $c->AppConfig->getConfig(
        'default_classes.router'
      );
      return new $router_class(
        $c->ResourceFactory,
        $c->ResourceLister,
        $c->Debug
      );
    };
    
    $this->MessageFilterFactory = $this->asShared(
      function(\Pimple $c) {
        return new MessageFilterFactory($c->AppConfig, $c->Debug);
      }
    );
    
    $this->ResourceFactory = function(\Pimple $c) {
      return new ResourceFactory(
        $c->TemplatePackageProvider,
        $c->TemplateSimpleRenderer,
        $c->AppConfig
      );
    };
    
    $this->StatusCodeMessages = function(\Pimple $c) {
      $status_messages = $c->AppConfig->getConfig(
        'default_classes.status_messages'
      );
      return new $status_messages;
    };
    
    // TODO: Refactor this
    $this->AppConfig = $this->asShared(
      function(\Pimple $c) {
        $app_config_class = $c->ApplicationConfigClass;
        $app_config = new $app_config_class;
        if ('development' == $app_config->getConfig('mode')) {
          $app_config->importConfig($c->ConfigDevelopment);
        };
        $app_config->importConfig($c->ConfigDefault);
        $app_config->importConfig($c->config);
        return $app_config;
      }
    );
    
    $this->StartupConfig = function(\Pimple $c) {
      return $c->ConfigDefault;
    };
    
    $this->ConfigDefault = function(\Pimple $c) {
      return new Config\DefaultConfig;
    };
    
    $this->TemplatePackageProvider = function(\Pimple $c) {
      return new TemplatePackageProvider(
        $c->TemplateLocator,
        $c->TemplateFactoryWithRegisteredEngines
      );
    };
    
    $this->TemplateFactoryWithRegisteredEngines = function(\Pimple $c) {
      $template_factory = $c->TemplateFactory;
      foreach (
        $c->RegisteredTemplateEngines as $filetype => $engine_class
      ) {
        call_user_func_array(
          array($template_factory, 'registerTemplateEngine'),
          array($filetype, $engine_class)
        );
      };
      return $template_factory;
    };
    
    $this->RegisteredTemplateEngines = function(\Pimple $c) {
      return $c->AppConfig->getConfig('template_engines');
    };
    
    $this->TemplateFactory = $this->asShared(function(\Pimple $c) {
      return new TemplateFactory($c->Debug);
    });
    
    $this->ConfigDevelopment = function(\Pimple $c) {
      return new Config\Development;
    };
    
    $this->TemplateLocator = function(\Pimple $c) {
      return new TemplateLocator(
        $c->ContentNegotiator,
        $c->AppPath,
        $c->EngineExtensions
      );
    };
    
    $this->EngineExtensions = function(\Pimple $c) {
      return array_keys($c->RegisteredTemplateEngines);
    };
    
    $this->ContentNegotiator = function(\Pimple $c) {
      return new ContentNegotiator;
    };
    
    $this->ResourceLister = function(\Pimple $c) {
      return new ResourceLister($c->ApplicationFinder);
    };
    
    $this->TemplateSimpleRenderer = function(\Pimple $c) {
      return new TemplateSimpleRenderer;
    };
    
    $this->FileSearcher = $this->asShared(function(\Pimple $c) {
      return new FileSearcher;
    });
    
    $this->Debug = $this->asShared(function(\Pimple $c) {
      return new Debug;
    });
    
    $this->RequestFilters = function(\Pimple $c) {
      return $c->filterBuilder($c, 'request_filters');
    };
    
    $this->ResponseFilters = function(\Pimple $c) {
      return $c->filterBuilder($c, 'response_filters');
    };
        
    $this->AppPath = function(\Pimple $c) {
      return $c->ApplicationFinder->find($c->app_name);
    };
    
    $this->ApplicationFinder = function(\Pimple $c) {
      return new Application\Finder;
    };
    
    $this->ApplicationClass = function(\Pimple $c) {
      $test = $c->app_name . '\Application';
      if (class_exists($test)) {
        return $test;
      };
      return $c->StartupConfig->getConfig('default_classes.application');
    };

    $this->ApplicationConfigClass = function(\Pimple $c) {
      $test = $c->app_name . '\Config';
      if (class_exists($test)) {
        return $test;
      };
      return $c->StartupConfig->getConfig('default_classes.config');
    };
  }
  
  function filterBuilder(\Pimple $c, $type) {
    $filter_factory = $c->MessageFilterFactory;
    $filters = array();
    foreach (
      $c->AppConfig->getConfig($type) as $key => $filter
    ) {
      $filters[$key] = $filter_factory->getFilter($filter);
    };
    return $filters;
  }
  
}
