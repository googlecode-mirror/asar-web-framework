<?php

namespace Asar;

class Injector {
  
  private static $cli;
  
  static function injectApplicationRunner(EnvironmentScope $env_scope) {
    return new ApplicationRunner(
    );
  }
  
  static function injectEnvironmentHelper(EnvironmentScope $env_scope) {
    return new EnvironmentHelper\Web(
      self::injectDefaultConfig($env_scope),
      self::injectRequestFactory($env_scope),
      self::injectResponseExporter($env_scope),
      self::injectServerVars($env_scope),
      self::injectGetVars($env_scope),
      self::injectPostVars($env_scope)
    );
  }
  
  static function injectApplicationName(EnvironmentScope $env_scope) {
    return $env_scope->getAppName();
  }
  
  static function injectClassLoader(EnvironmentScope $env_scope) {
    return new ClassLoader;
  }
  
  static function injectRequestFactory(EnvironmentScope $env_scope) {
    return new RequestFactory;
  }
  
  static function injectResponseExporter(EnvironmentScope $env_scope) {
    return new ResponseExporter;
  }
  
  static function injectApplicationFactory(EnvironmentScope $env_scope) {
    return new ApplicationFactory(
      self::injectDefaultConfig($env_scope)
    );
  }
  
  static function injectServerVars(EnvironmentScope $env_scope) {
    return $env_scope->getServerVars();
  }
  
  static function injectGetVars(EnvironmentScope $env_scope) {
    return $env_scope->getGetVars();
  }
  
  static function injectPostVars(EnvironmentScope $env_scope) {
    return $env_scope->getPostVars();
  }
  
  static function injectFileSearcher(EnvironmentScope $env_scope) {
    return new FileSearcher;
  }
  
  static function injectFileIncludeManager(EnvironmentScope $env_scope) {
    return new FileIncludeManager;
  }
  
  static function injectDefaultConfig(EnvironmentScope $env_scope) {
    return new Config\DefaultConfig;
  }
  
}
