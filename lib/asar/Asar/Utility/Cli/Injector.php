<?php
namespace Asar\Utility\Cli;

class Injector {
  
  private static $cli;
  
  static function injectEnvironmentHelperCli(EnvironmentScope $env_scope) {
    return new \Asar\EnvironmentHelper\Cli(
      self::injectCli($env_scope),
      self::injectArgv($env_scope),
      self::injectInitialTaskLists($env_scope),
      self::injectCliTaskFileLoader($env_scope)
    );
  }
  
  static function injectCli(EnvironmentScope $env_scope) {
    if (!self::$cli) {
      self::$cli =  new \Asar\Utility\Cli(
        self::injectCliInterpreter($env_scope), 
        self::injectCliExecutor($env_scope),
        self::injectCurrentWorkingDirectory($env_scope)
      );
    }
    return self::$cli;
  }
  
  static function injectCliInterpreter(EnvironmentScope $env_scope) {
    return new Interpreter;
  }
  
  static function injectCliExecutor(EnvironmentScope $env_scope) {
    return new Executor;
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
    return new BaseTasks;
  }

  static function injectUtilityCliFrameworkTasks(
    EnvironmentScope $env_scope
  ) {
    return new FrameworkTasks(
      self::injectFileHelper($env_scope)
    );
  }
  
  static function injectFileHelper(EnvironmentScope $env_scope) {
    return new \Asar\FileHelper;
  }
  
  static function injectCliTaskFileLoader(EnvironmentScope $env_scope) {
    return new TaskFileLoader(
      $env_scope->getCurrentWorkingDirectory(),
      self::injectUtilityClassFilePeek($env_scope),
      self::injectCli($env_scope)
    );
  }
  
  static function injectUtilityClassFilePeek(EnvironmentScope $env_scope) {
    return new \Asar\Utility\ClassFilePeek;
  }

}