<?php

class Asar_Utility_CLI {
  
  private $cwd;
  
  function __construct() {
    $this->cwd = getcwd();
  }
  
  function execute(array $arguments) {
    $exec = $this->interpret($arguments);
    if (is_array($exec) && array_key_exists('command', $exec)) {
      $method = 'task' . Asar_Utility_String::camelCase($exec['command']);
      call_user_func_array(
        array($this, $method), $exec['arguments']
      );
    }
    if ($exec['flags'] && array_search('version', $exec['flags']) !== FALSE) {
      echo 'Asar Web Framework ' . Asar::getVersion();
    }
  }
  
  function interpret(array $args) {
    $caller = array_shift($args);
    $flags = array();
    $arguments = array();
    $is_command_found = false;
    $result = array();
    foreach ($args as $arg) {
      if ($is_command_found) {
        $arguments[] = $arg;
      } elseif (strpos($arg, '--') === 0) {
        $flags[] = substr($arg, 2);
      } else {
        $result['command'] = $arg;
        $is_command_found = true;
      } 
    }
    $result['caller']    = $caller;
    $result['flags']     = $flags;
    $result['arguments'] = $arguments;
    return $result;
  }
  
  function __call($method, $args) {
    throw new Asar_Utility_CLI_Exception_UndefinedTask(
      "The task method '$method' is not defined."
    );
  }
  
  function taskCreateProject($directory, $appname) {
    $this->taskCreateProjectDirectories($directory, $appname);
    $this->taskCreateApplication($directory, $appname);
    $this->taskCreateFrontController($directory, $appname);
    $this->taskCreateResource($directory, $appname, '/');
    $this->taskCreateTasksFile($directory, $appname);
    $this->taskCreateHtaccessFile($directory);
  }
  
  function taskCreateFrontController($directory, $appname) {
    $this->taskCreateFile(
      Asar::constructPath(
        $this->getProjectPath($directory), 'web', 'index.php'
      ),
      "<?php\n" .
      "set_include_path(\n" .
      "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n" .
      "  realpath(dirname(__FILE__) . '/../vendor') . PATH_SEPARATOR .\n" .
      "  get_include_path()\n" .
      ");\n" .
      "require_once 'Asar.php';\n" .
      "Asar::start('$appname');\n"
    );
  }
  
  function taskCreateApplication($directory, $appname) {
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
  
  function taskCreateResource($directory, $appname, $url) {
    if ($url == '/') {
      $resource_name = '_Index';
      $resource_path = 'Index';
    } else {
      $resource_name_arr = explode('/', $url);
      $resource_name_arr = array_map(
        array('Asar_Utility_String', 'camelCase'), $resource_name_arr
      );
      $resource_name = implode('_', $resource_name_arr);
      $resource_path = implode('/', $resource_name_arr);
    }
    $this->taskCreateFile(
      Asar::constructPath(
        $this->getProjectPath($directory), 'apps', $appname, 
        'Resource', ltrim($resource_path, '/') . '.php'
      ),
      "<?php\n" .
        "class " . $appname . "_Resource" . $resource_name . " extends Asar_Resource {\n" .
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
      '', // project_root
      'apps', 'vendor', 'web', 'tests', 'logs'
    );
    if ($app_name) {
      $app_path = Asar::constructPath('apps', $app_name);
      $directories = array_merge($directories, array(
        $app_path, "$app_path/Resource", "$app_path/Representation"
      ));
    }
    
    foreach ($directories as $directory) {
      mkdir(Asar::constructPath($project_path, $directory));
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
    Asar_File::create($path)->write($contents)->save();
    echo "\nCreated: " . str_replace($this->cwd, '', $path);
  }

}
