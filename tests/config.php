<?php
ini_set('error_reporting', E_ALL | E_STRICT);

require_once realpath(dirname(__FILE__) . '/../lib/core/Asar.php');

$__asar = Asar::getInstance();
$__asar->getToolSet()->getIncludePathManager()->add(
  $__asar->getFrameworkCorePath(),
  $__asar->getFrameworkDevTestingPath(),
  $__asar->getFrameworkExtensionsPath()
);

require_once 'Asar/EnvironmentScope.php';
require_once 'Asar/Injector.php';

if (!isset($_SESSION)) {
  $_SESSION = array();
}
$scope = new Asar_EnvironmentScope(
  'Foo', $_SERVER, $_GET, $_POST, $_FILES, $_SESSION, $_COOKIE, $_ENV, getcwd()
);
Asar_Injector::injectEnvironmentHelperBootstrap($scope)->run();
