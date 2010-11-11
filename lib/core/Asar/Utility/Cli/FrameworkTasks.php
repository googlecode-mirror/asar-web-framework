<?php

class Asar_Utility_Cli_FrameworkTasks implements Asar_Utility_Cli_Interface {
  
  private $controller, $file_helper, $cwd;
  
  function __construct(Asar_FileHelper $file_helper) {
    $this->file_helper = $file_helper;
  }
  
  function setController(Asar_Utility_Cli $controller) {
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
    } catch (Asar_FileHelper_Exception_FileAlreadyExists $e) {
      $this->out('Skipped - File exists: ' . $filename);
    }
  }
  
  function taskCreateHtaccessFile($path) {
    $this->taskCreateFile(
      $this->constructPath($path, 'web', '.htaccess'),
      "<IfModule mod_rewrite.c>\n" .
      "RewriteEngine On\n".
      "RewriteBase /\n".
      "RewriteCond %{REQUEST_FILENAME} !-f\n".
      "RewriteCond %{REQUEST_FILENAME} !-d\n".
      "RewriteRule . /index.php [L]\n".
      "</IfModule>\n"
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
  
  private function constructPath() {
    $subpaths = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $subpaths);
  }
  
  function taskCreateDirectory($dir) {
    try {
      $this->file_helper->createDir($this->getFullPath($dir));
      $this->out("Created: $dir");
    } catch (Asar_FileHelper_Exception_DirectoryAlreadyExists $e) {
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
    $this->taskCreateHtaccessFile($path);
    $this->taskCreateTestConfigFile($path);
  }
  
}
