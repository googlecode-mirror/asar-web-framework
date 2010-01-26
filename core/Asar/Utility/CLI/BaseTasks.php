<?php

class Asar_Utility_CLI_BaseTasks implements Asar_Utility_CLI_Interface {
  
  private $cwd;
  private $controller = null;
  
  function __construct() {
    $this->cwd = getcwd();
  }
  
  function setController(Asar_Utility_CLI $controller) {
    $this->controller = $controller;
  }
  
  function taskCreateProject($directory, $appname) {
    $this->taskCreateProjectDirectories($directory, $appname);
    $this->taskCreateApplication($appname, $directory);
    $this->taskCreateFrontController($directory, $appname);
    $this->taskCreateResource('/', $appname, $directory);
    $this->taskCreateTasksFile($directory, $appname);
    $this->taskCreateHtaccessFile($directory);
    $this->taskCreateTestConfigFile($directory);
  }
  
  function taskCreateFrontController($directory, $appname) {
    $this->taskCreateFile(
      Asar::constructPath(
        $this->getProjectPath($directory), 'web', 'index.php'
      ),
      "<?php\n" .
      "set_include_path(\n" .
      "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n" .
      "  realpath(dirname(__FILE__) . '/../lib/vendor') . PATH_SEPARATOR .\n" .
      "  get_include_path()\n" .
      ");\n" .
      "require_once 'Asar.php';\n" .
      "Asar::start('$appname');\n"
    );
  }
  
  function taskCreateApplication($appname, $directory) {
    $this->taskCreateFile(
      Asar::constructPath(
        $this->getProjectPath($directory), 'apps', $appname, 'Application.php'
      ),
      "<?php\n" .
        "class " . $appname . "_Application extends Asar_Application {\n" .
        "  \n".
        "}\n"
    );
  }
  
  static function _formatPathLevel($path) {
    if ($path == '*') {
      return '_Item';
    }
    return Asar_Utility_String::camelCase($path);
  }
  
  function taskCreateResource($url, $appname = '', $directory = '') {
    if (file_exists('tasks.php')) {
      include 'tasks.php';
      $appname = $main_app;
    }
    if ($url == '/') {
      $this->_createResourceFile(array('Index'), $appname,  $directory);
    } else {
      $resource_name_arr = explode('/', ltrim($url, '/'));
      //array_shift($resource_name_arr);
      $resource_name_arr = array_map(
        array('self', '_formatPathLevel'), $resource_name_arr
      );
      $resource_name = array();
      foreach ($resource_name_arr as $level) {
        $resource_name[] = $level;
        $this->_createResourceFile(
          $resource_name, $appname, $directory
        );
      }
    }
  }
  
  private function _createResourceFile($name, $app, $directory) {
    $this->taskCreateFileAndDirectory(
      Asar::constructPath(
        $this->getProjectPath($directory), 'apps', $app, 
        'Resource', ltrim(implode('/', $name), '/') . '.php'
      ),
      "<?php\n" .
        "class " . $app . "_Resource_" . implode('_', $name) . " extends Asar_Resource {\n" .
        "  \n" .
        "  function GET() {\n".
        "    \n" .
        "  }\n" .
        "  \n" .
        "}\n"
    );
  }
  
  private function getProjectPath($project_name) {
    return $this->cwd . DIRECTORY_SEPARATOR . $project_name;
  }
  
  function taskCreateProjectDirectories($root, $app_name = '') {
    $project_path = $this->getProjectPath($root);
    $directories = array(
      '', // $project_path
      'apps', 'lib', 'lib/vendor', 'web', 'tests', 'tests/data',
      'tests/functional', 'tests/unit', 'logs'
    );
    if ($app_name) {
      $app_path = Asar::constructPath('apps', $app_name);
      $directories = array_merge($directories, array(
        $app_path, "$app_path/Resource", "$app_path/Representation"
      ));
    }
    
    foreach ($directories as $directory) {
      $this->taskCreateDirectory(Asar::constructPath($project_path, $directory));
    }
  }
  
  function taskCreateHtaccessFile($root) {
    $project_path = $this->getProjectPath($root);
    $this->taskCreateFile(
      $project_path . '/web/.htaccess',
      "<IfModule mod_rewrite.c>\n" .
        "RewriteEngine On\n".
        "RewriteBase /\n".
        "RewriteCond %{REQUEST_FILENAME} !-f\n".
        "RewriteCond %{REQUEST_FILENAME} !-d\n".
        "RewriteRule . /index.php [L]\n".
        "</IfModule>\n"
    );
  }
  
  function taskCreateTestConfigFile($root) {
    $project_path = $this->getProjectPath($root);
    $this->taskCreateFile(
      $project_path . '/tests/config.php',
      "<?php\n".
      "set_include_path(\n".
      "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n".
      "  realpath(dirname(__FILE__) . '/../lib/vendor') . PATH_SEPARATOR .\n".
      "  get_include_path()\n".
      ");\n"
    );
  }
  
  function taskCreateTasksFile($directory, $appname) {
    $this->taskCreateFile(
      Asar::constructPath(
        $this->getProjectPath($directory), 'tasks.php'
      ),
      "<?php\n" .
        '$main_app = \'' . $appname . '\';' . "\n"
    );
  }
  
  function taskCreateFile($path, $contents) {
    if (!file_exists($path)) {
      Asar_File::create($path)->write($contents)->save();
      echo "\nCreated: " . str_replace($this->cwd . DIRECTORY_SEPARATOR, '', $path);
    } else {
      echo "\nSkipped - File exists: " .
        str_replace($this->cwd . DIRECTORY_SEPARATOR, '', $path);;
    }
  }
  
  function taskCreateDirectory($path) {
    if (!file_exists($path)) {
      mkdir($path);
      echo "\nCreated: " . 
        str_replace($this->cwd . DIRECTORY_SEPARATOR, '', $path);
    } else {
      echo "\nSkipped - Directory exists: " . 
        str_replace($this->cwd . DIRECTORY_SEPARATOR, '', $path);
    }
  }
  
  function taskCreateFileAndDirectory($path, $contents) {
    //if (!file_exists(dirname($path))) {
      $this->taskCreateDirectory(dirname($path));
    //}
    $this->taskCreateFile($path, $contents);
  }
}