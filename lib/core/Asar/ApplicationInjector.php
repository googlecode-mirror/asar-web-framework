<?php

class Asar_ApplicationInjector {
  
  private static
    $loaded_filters = array();
  
  static function injectApplicationRunner(Asar_ApplicationScope $scope) {
    return new Asar_ApplicationRunner(
      self::injectApplication($scope)
    );
  }
  
  static function injectRequest(Asar_ApplicationScope $scope) {
    return $scope->getRequest();
  }
  
  static function injectApplication(Asar_ApplicationScope $scope) {
    $app_full_name = self::getApplicationClass($scope);
    return new $app_full_name(
      $scope->getAppName(),
      self::injectRouter($scope),
      self::injectStatusCodeMessages($scope),
      self::injectAppConfig($scope)->getConfig('map'),
      self::injectRequestFilters($scope),
      self::injectResponseFilters($scope)
    );
  }
  
  static function injectRouter(Asar_ApplicationScope $scope) {
    $router_class = self::injectAppConfig($scope)->getConfig(
      'default_classes.router'
    );
    return new $router_class(
      self::injectResourceFactory($scope),
      self::injectResourceLister($scope),
      self::injectDebug($scope)
    );
  }
  
  static function injectMessageFilterFactory(Asar_ApplicationScope $scope) {
    if (!$scope->isInCache('MessageFilterFactory')) {
      $scope->addToCache(
        'MessageFilterFactory', new Asar_MessageFilterFactory(
          self::injectAppConfig($scope), self::injectDebug($scope)
        )
      );
    }
    return $scope->getCache('MessageFilterFactory');
  }
  
  static function injectResourceFactory(Asar_ApplicationScope $scope) {
    return new Asar_ResourceFactory(
      self::injectTemplatePackageProvider($scope),
      self::injectTemplateSimpleRenderer($scope),
      self::injectAppConfig($scope)
    );
  }
  
  static function injectStatusCodeMessages(Asar_ApplicationScope $scope) {
    $status_messages = self::injectAppConfig($scope)->getConfig(
      'default_classes.status_messages'
    );
    return new $status_messages;
  }
  
  // TODO: Refactor this
  static function injectAppConfig(Asar_ApplicationScope $scope) {
    if (!$scope->isInCache('AppConfig')) {
      $app_config_class = self::getApplicationConfigClass($scope);
      $app_config = new $app_config_class;
      if ('development' == $app_config->getConfig('mode')) {
        $app_config->importConfig(self::injectConfigDevelopment($scope));
      }
      $app_config->importConfig(self::injectConfigDefault($scope));
      $scope->addToCache('AppConfig', $app_config);
    }
    return $scope->getCache('AppConfig');
  }
  
  static function injectConfigDefault(Asar_ApplicationScope $scope) {
    return new Asar_Config_Default;
  }
  
  static function injectTemplatePackageProvider(Asar_ApplicationScope $scope) {
    return new Asar_TemplatePackageProvider(
      self::injectTemplateLocator($scope),
      self::injectTemplateFactoryWithRegisteredEngines($scope)
    );
  }
  
  static function injectTemplateFactoryWithRegisteredEngines(Asar_ApplicationScope $scope) {
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
  
  static function injectRegisteredTemplateEngines(Asar_ApplicationScope $scope) {
    return self::injectAppConfig($scope)->getConfig('template_engines');
  }
  
  static function injectTemplateFactory(Asar_ApplicationScope $scope) {
    if (!$scope->isInCache('TemplateFactory')) {
      $scope->addToCache(
        'TemplateFactory', new Asar_TemplateFactory(self::injectDebug($scope))
      );
    }
    return $scope->getCache('TemplateFactory');
  }
  
  static function injectConfigDevelopment(Asar_ApplicationScope $scope) {
    return new Asar_Config_Development;
  }
  
  static function injectTemplateLocator(Asar_ApplicationScope $scope) {
    return new Asar_TemplateLocator(
      self::injectContentNegotiator($scope),
      self::injectAppPath($scope),
      self::injectEngineExtensions($scope)
    );
  }
  
  static function injectEngineExtensions(Asar_ApplicationScope $scope) {
    return array_keys(self::injectRegisteredTemplateEngines($scope));
  }
  
  static function injectContentNegotiator(Asar_ApplicationScope $scope) {
    return new Asar_ContentNegotiator;
  }
  
  static function injectResourceLister(Asar_ApplicationScope $scope) {
    return new Asar_ResourceLister(self::injectFileSearcher($scope));
  }
  
  static function injectTemplateSimpleRenderer(Asar_ApplicationScope $scope) {
    return new Asar_TemplateSimpleRenderer;
  }
  
  static function injectFileSearcher(Asar_ApplicationScope $scope) {
    if (!$scope->isInCache('FileSearcher')) {
      $scope->addToCache('FileSearcher', new Asar_FileSearcher);
    }
    return $scope->getCache('FileSearcher');
  }
  
  static function injectDebug(Asar_ApplicationScope $scope) {
    if (!$scope->isInCache('Debug')) {
      $scope->addToCache('Debug', new Asar_Debug);
    }
    return $scope->getCache('Debug');
  }
  
  static function injectRequestFilters(Asar_ApplicationScope $scope) {
    return self::filterBuilder($scope, 'request_filters');
  }
  
  static function injectResponseFilters(Asar_ApplicationScope $scope) {
    return self::filterBuilder($scope, 'response_filters');
  }
  
  static function filterBuilder(Asar_ApplicationScope $scope, $type) {
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
  
  static function injectAppPath(Asar_ApplicationScope $scope) {
    return self::injectFileSearcher($scope)->find($scope->getAppName());
  }
  
  static function getApplicationClass(Asar_ApplicationScope $scope) {
    $test = $scope->getAppName() . '_Application';
    if (class_exists($test)) {
      return $test;
    }
    return $scope->getConfig()->getConfig('default_classes.application');
  }

  static function getApplicationConfigClass(Asar_ApplicationScope $scope) {
    $test = $scope->getAppName() . '_Config';
    if (class_exists($test)) {
      return $test;
    } 
    return $scope->getConfig()->getConfig('default_classes.config');
  }
  
}
