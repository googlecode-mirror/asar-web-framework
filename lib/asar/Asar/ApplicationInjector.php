<?php
namespace Asar;

/**
 */
class ApplicationInjector {
  
  static function injectApplicationRunner(ApplicationScope $scope) {
    return new ApplicationRunner(
      self::injectApplication($scope)
    );
  }
  
  static function injectRequest(ApplicationScope $scope) {
    return $scope->getRequest();
  }
  
  static function injectApplication(ApplicationScope $scope) {
    $app_full_name = self::getApplicationClass($scope);
    self::registerLoggers($scope);
    return new $app_full_name(
      $scope->getAppName(),
      self::injectRouter($scope),
      self::injectStatusCodeMessages($scope),
      self::injectAppConfig($scope)->getConfig('map'),
      self::injectRequestFilters($scope),
      self::injectResponseFilters($scope)
    );
  }
  
  static function registerLoggers(ApplicationScope $scope) {
    if (self::injectLogFile($scope)) {
      Logger\Registry::register(
        $scope->getAppName(), self::injectLogFile($scope)
      );
    }
  }
  
  static function injectLogFile(ApplicationScope $scope) {
    return self::injectAppConfig($scope)->getConfig('log_file');
  }
  
  static function injectRouter(ApplicationScope $scope) {
    $router_class = self::injectAppConfig($scope)->getConfig(
      'default_classes.router'
    );
    return new $router_class(
      self::injectResourceFactory($scope),
      self::injectResourceLister($scope),
      self::injectDebug($scope)
    );
  }
  
  static function injectMessageFilterFactory(ApplicationScope $scope) {
    if (!$scope->isInCache('MessageFilterFactory')) {
      $scope->addToCache(
        'MessageFilterFactory', new MessageFilterFactory(
          self::injectAppConfig($scope), self::injectDebug($scope)
        )
      );
    }
    return $scope->getCache('MessageFilterFactory');
  }
  
  static function injectResourceFactory(ApplicationScope $scope) {
    return new ResourceFactory(
      self::injectTemplatePackageProvider($scope),
      self::injectTemplateSimpleRenderer($scope),
      self::injectAppConfig($scope)
    );
  }
  
  static function injectStatusCodeMessages(ApplicationScope $scope) {
    $status_messages = self::injectAppConfig($scope)->getConfig(
      'default_classes.status_messages'
    );
    return new $status_messages;
  }
  
  // TODO: Refactor this
  static function injectAppConfig(ApplicationScope $scope) {
    if (!$scope->isInCache('AppConfig')) {
      $app_config_class = self::getApplicationConfigClass($scope);
      $app_config = new $app_config_class;
      if ('development' == $app_config->getConfig('mode')) {
        $app_config->importConfig(self::injectConfigDevelopment($scope));
      }
      $app_config->importConfig(self::injectConfigDefault($scope));
      $app_config->importConfig($scope->getConfig());
      $scope->addToCache('AppConfig', $app_config);
    }
    return $scope->getCache('AppConfig');
  }
  
  static function injectStartupConfig(ApplicationScope $scope) {
    return self::injectConfigDefault($scope);
  }
  
  static function injectConfigDefault(ApplicationScope $scope) {
    return new Config\DefaultConfig;
  }
  
  static function injectTemplatePackageProvider(ApplicationScope $scope) {
    return new TemplatePackageProvider(
      self::injectTemplateLocator($scope),
      self::injectTemplateFactoryWithRegisteredEngines($scope)
    );
  }
  
  static function injectTemplateFactoryWithRegisteredEngines(ApplicationScope $scope) {
    $template_factory = self::injectTemplateFactory($scope);
    foreach (
      self::injectRegisteredTemplateEngines($scope) as $filetype => $engine_class
    ) {
      call_user_func_array(
        array($template_factory, 'registerTemplateEngine'),
        array($filetype, $engine_class)
      );
    }
    return $template_factory;
  }
  
  static function injectRegisteredTemplateEngines(ApplicationScope $scope) {
    return self::injectAppConfig($scope)->getConfig('template_engines');
  }
  
  static function injectTemplateFactory(ApplicationScope $scope) {
    if (!$scope->isInCache('TemplateFactory')) {
      $scope->addToCache(
        'TemplateFactory', new TemplateFactory(self::injectDebug($scope))
      );
    }
    return $scope->getCache('TemplateFactory');
  }
  
  static function injectConfigDevelopment(ApplicationScope $scope) {
    return new Config\Development;
  }
  
  static function injectTemplateLocator(ApplicationScope $scope) {
    return new TemplateLocator(
      self::injectContentNegotiator($scope),
      self::injectAppPath($scope),
      self::injectEngineExtensions($scope)
    );
  }
  
  static function injectEngineExtensions(ApplicationScope $scope) {
    return array_keys(self::injectRegisteredTemplateEngines($scope));
  }
  
  static function injectContentNegotiator(ApplicationScope $scope) {
    return new ContentNegotiator;
  }
  
  static function injectResourceLister(ApplicationScope $scope) {
    return new ResourceLister(self::injectApplicationFinder($scope));
  }
  
  static function injectTemplateSimpleRenderer(ApplicationScope $scope) {
    return new TemplateSimpleRenderer;
  }
  
  static function injectFileSearcher(ApplicationScope $scope) {
    if (!$scope->isInCache('FileSearcher')) {
      $scope->addToCache('FileSearcher', new FileSearcher);
    }
    return $scope->getCache('FileSearcher');
  }
  
  static function injectDebug(ApplicationScope $scope) {
    if (!$scope->isInCache('Debug')) {
      $scope->addToCache('Debug', new Debug);
    }
    return $scope->getCache('Debug');
  }
  
  static function injectRequestFilters(ApplicationScope $scope) {
    return self::filterBuilder($scope, 'request_filters');
  }
  
  static function injectResponseFilters(ApplicationScope $scope) {
    return self::filterBuilder($scope, 'response_filters');
  }
  
  static function filterBuilder(ApplicationScope $scope, $type) {
    $filter_factory = self::injectMessageFilterFactory($scope);
    foreach (
      self::injectAppConfig($scope)->getConfig($type) as $key => $filter
    ) {
      if (!$scope->isInCache($filter)) {
        $scope->addToCache($filter, $filter_factory->getFilter($filter));
      }
      $filters[$key] = $scope->getCache($filter);
    }
    return $filters;
  }
  
  static function injectAppPath(ApplicationScope $scope) {
    return self::injectApplicationFinder($scope)->find($scope->getAppName());
  }
  
  static function injectApplicationFinder(ApplicationScope $scope) {
    return new Application\Finder;
  }
  
  static function getApplicationClass(ApplicationScope $scope) {
    $test = $scope->getAppName() . '\Application';
    if (class_exists($test)) {
      return $test;
    }
    return self::injectStartupConfig($scope)->getConfig('default_classes.application');
  }

  static function getApplicationConfigClass(ApplicationScope $scope) {
    $test = $scope->getAppName() . '\Config';
    if (class_exists($test)) {
      return $test;
    }
    return self::injectStartupConfig($scope)->getConfig('default_classes.config');
  }
  
}
