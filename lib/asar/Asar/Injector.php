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
      self::injectConfigDefault($env_scope),
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
  
  static function injectEnvironmentHelperCli(EnvironmentScope $env_scope) {
    return new EnvironmentHelper\Cli(
      self::injectCli($env_scope),
      self::injectArgv($env_scope),
      self::injectInitialTaskLists($env_scope),
      self::injectCliTaskFileLoader($env_scope)
    );
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
      self::injectConfigDefault($env_scope)
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
  
  static function injectCli(EnvironmentScope $env_scope) {
    if (!self::$cli) {
      self::$cli =  new Utility\Cli(
        self::injectCliInterpreter($env_scope), 
        self::injectCliExecutor($env_scope),
        self::injectCurrentWorkingDirectory($env_scope)
      );
    }
    return self::$cli;
  }
  
  static function injectCliInterpreter(EnvironmentScope $env_scope) {
    return new Utility\Cli\Interpreter;
  }
  
  static function injectCliExecutor(EnvironmentScope $env_scope) {
    return new Utility\Cli\Executor;
  }
  
  static function injectArgv(EnvironmentScope $env_scope) {
    return $env_scope->getArgv();
  }
  
  static function injectCurrentWorkingDirectory(
    EnvironmentScope $env_scope
  ) {
    return $env_scope->getCurrentWorkingDirectory();
  }
  
  static function injectInitialTaskLists(EnvironmentScope $env_scope) {
    return array(
      self::injectUtilityCliBaseTasks($env_scope),
      self::injectUtilityCliFrameworkTasks($env_scope)
    );
  }
  
  static function injectUtilityCliBaseTasks(EnvironmentScope $env_scope) {
    return new Utility\Cli\BaseTasks;
  }

  static function injectUtilityCliFrameworkTasks(
    EnvironmentScope $env_scope
  ) {
    return new Utility\Cli\FrameworkTasks(
      self::injectFileHelper($env_scope)
    );
  }
  
  static function injectFileHelper(EnvironmentScope $env_scope) {
    return new FileHelper;
  }
  
  static function injectConfigDefault(EnvironmentScope $env_scope) {
    return new Config_Default;
  }
  
  static function injectCliTaskFileLoader(EnvironmentScope $env_scope) {
    return new Utility\Cli\TaskFileLoader(
      $env_scope->getCurrentWorkingDirectory(),
      self::injectUtilityClassFilePeek($env_scope),
      self::injectCli($env_scope)
    );
  }
  
  static function injectUtilityClassFilePeek(EnvironmentScope $env_scope) {
    return new Utility\ClassFilePeek;
  }
}
