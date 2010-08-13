<?php
/*
set_include_path(
  realpath(dirname(__FILE__) . '/../../../lib/core') . PATH_SEPARATOR .
  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .
  realpath(dirname(__FILE__) . '/../lib/vendor') . PATH_SEPARATOR .
  get_include_path()
);*/
require_once realpath(dirname(__FILE__) . '/../../../lib/core/Asar.php');
//Asar::start('Sample');
$__asar = Asar::getInstance();
$__asar->getToolSet()->getIncludePathManager()->add(
  $__asar->getFrameworkCorePath(),
  realpath(dirname(__FILE__) . '/../apps')
);

require_once 'Asar/EnvironmentScope.php';
require_once 'Asar/Injector.php';

if (!isset($_SESSION)) {
  $_SESSION = array();
}
if (!isset($argv)) {
  $argv = array();
}
$scope = new Asar_EnvironmentScope(
  $_SERVER, $_GET, $_POST, $_FILES, $_SESSION, $_COOKIE, $_ENV, getcwd(), $argv
);
Asar_Injector::injectEnvironmentHelperBootstrap($scope)->run();
Asar_Injector::injectEnvironmentHelper($scope)->runAppInProductionEnvironment('Sample');
