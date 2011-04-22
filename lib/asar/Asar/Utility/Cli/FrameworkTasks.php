<?php

namespace Asar\Utility\Cli;

use \Asar;
use \Asar\Utility\Cli;
use \Asar\Utility\String;
use \Asar\FileHelper;
use \Asar\FileHelper\Exception\FileAlreadyExists;
use \Asar\FileHelper\Exception\DirectoryAlreadyExists;

/**
 */
class FrameworkTasks implements CliInterface {
  
  private $controller, $file_helper, $cwd;
  
  function __construct(FileHelper $file_helper) {
    $this->file_helper = $file_helper;
  }
  
  function setController(Cli $controller) {
    $this->controller = $controller;
    $this->cwd = $this->controller->getWorkingDirectory();
  }
  
  private function out($string) {
    $this->controller->out($string);
  }
  
  private function getFullPath($path) {
    return $this->cwd . DIRECTORY_SEPARATOR . $path;
  }
  
  function taskCreateFile($filename, $contents) {
    try {
      $result = $this->file_helper->create(
        $this->getFullPath($filename), $contents
      );    
      if ($result) {
        $this->out('Created: ' . $filename);
      }
    } catch (FileAlreadyExists $e) {
      $this->out('Skipped - File exists: ' . $filename);
    }
  }
  
  function taskCreateHtaccessFile($project) {
    $this->taskCreateFile(
      $this->constructPath($project, 'web', '.htaccess'),
      "<IfModule mod_rewrite.c>\n" .
      "RewriteEngine On\n".
      "RewriteBase /\n".
      "RewriteCond %{REQUEST_FILENAME} !-f\n".
      "RewriteCond %{REQUEST_FILENAME} !-d\n".
      "RewriteRule . /index.php [L]\n".
      "</IfModule>\n"
    );
  }
  
  function taskCreateBootstrap($project) {
    $asar_arc_class_path = $this->constructPath(
      Asar::getInstance()->getFrameworkCorePath(), 'Asar.php'
    );
    $this->taskCreateFile(
      $this->constructPath($project, 'bootstrap.php'),
      "<?php\n" .
      "// Change the path when appropriate\n" .
      "require_once realpath('$asar_arc_class_path');\n" .
      "\n" .
      "// This runs the whole bootsrap process inside a function\n" .
      "// so we don't pollute the global scope.\n" .
      "function _bootstrap() {\n" .
      "  // Prepares the include paths\n" .
      "  \$__asar = Asar::getInstance();\n" .
      "  \$__asar->getToolSet()->getIncludePathManager()->add(\n" .
      "    \$__asar->getFrameworkCorePath(),\n" .
      "    realpath(dirname(__FILE__) . '/apps')\n" .
      "  );\n" .
      "  require_once 'Asar/EnvironmentScope.php';\n" .
      "  require_once 'Asar/Injector.php';\n" .
      "  if (!isset(\$_SESSION)) {\n" .
      "    \$_SESSION = array();\n" .
      "  }\n" .
      "  if (!isset(\$argv)) {\n" .
      "    \$argv = array();\n" .
      "  }\n" .
      "  // Load the environment variables\n" .
      "  \$scope = new Asar_EnvironmentScope(\n" .
      "    \$_SERVER, \$_GET, \$_POST, \$_FILES, \$_SESSION, \$_COOKIE, \$_ENV, getcwd()\n" .
      "  );\n" .
      "  // Run initial bootstrap \n" .
      "  // We load the class loader here\n" .
      "  Asar_Injector::injectEnvironmentHelperBootstrap(\$scope)->run();\n" .
      "  return Asar_Injector::injectEnvironmentHelper(\$scope);\n" .
      "}\n" .
      "\n" .
      "return _bootstrap();\n"
    );
  }
  
  function taskCreateApplicationConfig($project, $app = '') {
    $this->taskCreateFile(
      $this->constructPath($project, 'apps', $app, 'Config.php'),
      "<?php\n" .
      "class {$app}_Config extends Asar_Config {\n" .
      "\n" .
      "  // Add configuration directives here...\n" .
      "  protected \$config = array(\n" .
      "    // e.g.:\n" .
      "    // 'use_templates' => false,\n" .
      "  );\n".
      "}\n"
    );
  }
  
  function taskCreateFrontController($project, $app) {
    $this->taskCreateFile(
      $this->constructPath($project, 'web', 'index.php'),
      "<?php\n" .
      "\$env_helper = require realpath(dirname(__FILE__) . '/../bootstrap.php');\n" .
      "\$env_helper->runAppInProductionEnvironment('$app');\n"
    );
  }
  
  function taskCreateTestConfigFile($path) {
    $this->taskCreateFile(
      $this->constructPath($path, 'tests', 'config.php'),
      "<?php\n" .
      "ini_set('error_reporting', E_ALL | E_STRICT);\n" .
      "require_once realpath(dirname(__FILE__) . '/../lib/core/Asar.php');\n" .
      "\$__asar = Asar::getInstance();\n" .
      "\$__asar->getToolSet()->getIncludePathManager()->add(\n" .
      "  \$__asar->getFrameworkCorePath(),\n" .
      "  \$__asar->getFrameworkDevTestingPath(),\n" .
      "  \$__asar->getFrameworkExtensionsPath()\n" .
      ");\n" .
      "require_once 'Asar/EnvironmentScope.php';\n" .
      "require_once 'Asar/Injector.php';\n" .
      "if (!isset(\$_SESSION)) {\n" .
      "  \$_SESSION = array();\n" .
      "}\n" .
      "\$scope = new Asar_EnvironmentScope(\n" .
      "  \$_SERVER, \$_GET, \$_POST, \$_FILES, \$_SESSION, \$_COOKIE, \$_ENV, getcwd()\n" .
      ");\n" .
      "Asar_Injector::injectEnvironmentHelperBootstrap(\$scope)->run();\n" .
      "Asar_Injector::injectEnvironmentHelper(\$scope)->runTestEnvironment();\n" .
      "\n"
    );
  }
  
  function createProjectFile($project, $file, $contents) {
    if ($project == '.') {
      $this->taskCreateFile($file, $contents);
      return;
    }
    $this->taskCreateFile(
      $this->constructPath($project, $file), $contents
    );
  }
  
  function taskCreateTasksFile($project) {
    $this->taskCreateFile(
      $this->constructPath($project, 'tasks.php'),
      "<?php\n" .
      "\n" .
      "// This is a sample task\n" .
      "class MySampleTaskList implements Asar_Utility_Cli_Interface {\n" .
      "\n" .
      "  private \$controller;\n" .
      "\n" .
      "  function setController(Asar_Utility_Cli \$controller) {\n" .
      "    \$this->controller = \$controller;\n" .
      "  }\n" .
      "\n" .
      "  // You can call this task on the command line e.g.:\n" .
      "  // asarwf mysample:say-hello\n" .
      "  function taskSayHello() {\n" .
      "    //echo \"Hello World!\";\n" .
      "    \$this->controller->out(\"Hello World!\");\n" .
      "  }\n" .
      "\n" .
      "  function getTaskNamespace() {\n" .
      "    return 'mysample';\n" .
      "  }\n" .
      "\n" .
      "}\n"
    );
  }
  
  function taskCreateResource($project, $app, $url) {
    if ($url == '/') {
      $name = 'Index';
    } else {
      $levels = array_map(
        array($this, 'formatResourceNameFragment'), explode('/', ltrim($url, '/'))
      );
      $name = implode('_', $levels);
      $this->createResourceDir($project, $app, $levels);
    }
    $full_resource_name = $app . '_Resource_' . $name;
    $fname = str_replace('_', '/', $name);
    $this->createProjectFile(
      $project, $this->constructPath('apps', $app, 'Resource', $fname . '.php'),
      "<?php\n" .
      "class $full_resource_name extends Asar_Resource {\n" .
      "  \n" .
      $this->getResourceContents($url) .
      "  \n" .
      "}\n"
    );
  }
  
  private function createResourceDir($project, $app, $levels) {
    array_pop($levels);
    if (count($levels) === 0) {
      return;
    }
    $subpath = $this->constructPath($project, 'apps', $app, 'Resource');
    foreach ($levels as $level) {
      $subpath .= DIRECTORY_SEPARATOR . $level;
      $this->taskCreateDirectory($subpath);
    }
  }
  
  private function getResourceContents($url) {
    if (strpos($url, '{') > -1) {
      return $this->getWildCardResourceContents($url);
    }
    return $this->getDefaultResourceContents($url);
  }
  
  private function getDefaultResourceContents($url) {
    return 
      "  function GET() {\n".
      "    return \"Hello from '$url'.\";\n" .
      "  }\n";
  }
  
  private function getWildCardResourceContents($url) {
    return
      "  function GET() {\n" .
      "    \$path = \$this->getPathComponents();\n" .
      "    return \"Hello from '" . $this->getWildCardUrl($url) . "'.\";\n" .
      "  }\n" .
      "  \n" .
      "  function qualify(\$path) {\n" .
      "    // run your path validation here...\n" .
      "    return \n" .
      $this->getWildCardValidations($url) .
      "  }\n";
  }
  
  private function getWildCardUrl($url) {
    $fragments = explode('/', trim($url, '/'));
    $rewritten = array();
    foreach ($fragments as $fragment) {
      if (String::startsWith($fragment, '{')) {
        $rewritten[] = '{$path[\'' . trim($fragment, '{}') . "']}";
      } else {
        $rewritten[] = $fragment;
      }
    }
    return '/' . implode('/', $rewritten);
  }
  
  private function getWildCardValidations($url) {
    $fragments = explode('/', trim($url, '/'));
    $validations = array();
    foreach ($fragments as $fragment) {
      if (String::startsWith($fragment, '{')) {
        $validations[] = '      preg_match(\'/.+/\', $path[\''. trim($fragment, '{}') . '\'])';
      }
    }
    return implode(" &&\n", $validations). ";\n";
  }
  
  private function formatResourceNameFragment($path) {
    if (String::startsWith($path, '{')) {
      return 'Rt'. String::camelCase(trim($path, '{}'));
    }
    return String::camelCase($path);
  }
  
  private function constructPath() {
    $subpaths = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $subpaths);
  }
  
  function taskCreateDirectory($dir) {
    try {
      $this->file_helper->createDir($this->getFullPath($dir));
      $this->out("Created: $dir");
    } catch (DirectoryAlreadyExists $e) {
      $this->out("Skipped - Directory exists: $dir");
    }
  }
  
  function taskCreateFileAndDirectory($filepath, $contents) {
    $this->taskCreateDirectory(dirname($filepath));
    $this->taskCreateFile($filepath, $contents);
  }
  
  function taskCreateProjectDirectories($path, $app = '') {
    $this->taskCreateDirectory($path);
    $subpaths = array('apps', 'lib', 'lib/vendor', 'web', 'tests', 'logs');
    if ($app) {
      $subpaths = array_merge($subpaths, array(
        "apps/$app", "apps/$app/Resource", "apps/$app/Representation")
      );
    }
    foreach ($subpaths as $subpath) {
      $this->taskCreateDirectory($this->constructPath($path, $subpath));
    }
  }
  
  function taskCreateProject($path, $app = '') {
    $this->taskCreateProjectDirectories($path, $app);
    $this->taskCreateApplicationConfig($path, $app);
    $this->taskCreateHtaccessFile($path);
    $this->taskCreateFrontController($path, $app);
    $this->taskCreateBootstrap($path);
    $this->taskCreateTestConfigFile($path);
    $this->taskCreateTasksFile($path);
    $this->taskCreateResource($path, $app, '/');
  }
  
  function getTaskNamespace() {}
  
}
