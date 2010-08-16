<?php

require 'ClassLoader.php';
require 'FileSearcher.php';
require 'FileIncludeManager.php';
require 'EnvironmentHelper.php';
require 'EnvironmentHelper/Bootstrap.php';

class Asar_Injector {
  
  static function injectEnvironmentHelper(Asar_EnvironmentScope $env_scope) {
    //($request_factory, $response_exporter, $app_factory, $server, $params, $post)
    return new Asar_EnvironmentHelper(
      self::injectRequestFactory($env_scope),
      self::injectResponseExporter($env_scope),
      self::injectApplicationFactory($env_scope),
      self::injectServerVars($env_scope),
      self::injectGetVars($env_scope),
      self::injectPostVars($env_scope)
    );
  }
  
  static function injectEnvironmentHelperBootstrap(Asar_EnvironmentScope $env_scope) {
    return new Asar_EnvironmentHelper_Bootstrap(
      self::injectClassLoader($env_scope)
    );
  }
  
  static function injectEnvironmentHelperCli(Asar_EnvironmentScope $env_scope) {
    return new Asar_EnvironmentHelper_Cli(
      self::injectCli($env_scope),
      self::injectArgv($env_scope),
      self::injectInitialTaskLists($env_scope)
    );
  }
  
  static function injectClassLoader(Asar_EnvironmentScope $env_scope) {
    return new Asar_ClassLoader(
      self::injectFileSearcher($env_scope),
      self::injectFileIncludeManager($env_scope)
    );
  }
  
  static function injectRequestFactory(Asar_EnvironmentScope $env_scope) {
    return new Asar_RequestFactory;
  }
  
  static function injectResponseExporter(Asar_EnvironmentScope $env_scope) {
    return new Asar_ResponseExporter;
  }
  
  static function injectApplicationFactory(Asar_EnvironmentScope $env_scope) {
    return new Asar_ApplicationFactory;
  }
  
  static function injectServerVars(Asar_EnvironmentScope $env_scope) {
    return $env_scope->getServerVars();
  }
  
  static function injectGetVars(Asar_EnvironmentScope $env_scope) {
    return $env_scope->getGetVars();
  }
  
  static function injectPostVars(Asar_EnvironmentScope $env_scope) {
    return $env_scope->getPostVars();
  }
  
  static function injectFileSearcher(Asar_EnvironmentScope $env_scope) {
    return new Asar_FileSearcher;
  }
  
  static function injectFileIncludeManager(Asar_EnvironmentScope $env_scope) {
    return new Asar_FileIncludeManager;
  }
  
  static function injectCli(Asar_EnvironmentScope $env_scope) {
    return new Asar_Utility_Cli(
      self::injectCliInterpreter($env_scope), 
      self::injectCliExecutor($env_scope),
      self::injectCurrentWorkingDirectory($env_scope)
    );
  }
  
  static function injectCliInterpreter(Asar_EnvironmentScope $env_scope) {
    return new Asar_Utility_Cli_Interpreter;
  }
  
  static function injectCliExecutor(Asar_EnvironmentScope $env_scope) {
    return new Asar_Utility_Cli_Executor;
  }
  
  static function injectArgv(Asar_EnvironmentScope $env_scope) {
    return $env_scope->getArgv();
  }
  
  static function injectCurrentWorkingDirectory(Asar_EnvironmentScope $env_scope) {
    return $env_scope->getCurrentWorkingDirectory();
  }
  
  static function injectInitialTaskLists(Asar_EnvironmentScope $env_scope) {
    return array(
      self::injectUtilityCliBaseTasks($env_scope),
      self::injectUtilityCliFrameworkTasks($env_scope)
    );
  }
  
  static function injectUtilityCliBaseTasks(Asar_EnvironmentScope $env_scope) {
    return new Asar_Utility_Cli_BaseTasks;
  }

  static function injectUtilityCliFrameworkTasks(Asar_EnvironmentScope $env_scope) {
    return new Asar_Utility_Cli_FrameworkTasks(self::injectFileHelper($env_scope));
  }
  
  static function injectFileHelper(Asar_EnvironmentScope $env_scope) {
    return new Asar_FileHelper;
  }
  
  static function injectConfigDefault(Asar_EnvironmentScope $env_scope) {
    return new Asar_Config_Default;
  }
}
