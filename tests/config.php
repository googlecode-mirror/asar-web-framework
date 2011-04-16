<?php
ini_set('error_reporting', E_ALL | E_STRICT);
/*
require_once realpath(dirname(__FILE__) . '/../lib/core/Asar.php');

$__asar = Asar::getInstance();
$__asar->getToolSet()->getIncludePathManager()->add(
  $__asar->getFrameworkDevTestingPath(),
  $__asar->getFrameworkExtensionsPath(),
  $__asar->getFrameworkVendorPath()
);
*/
$lib_path = realpath(dirname(__FILE__) . '/../lib/') . '/';
require_once $lib_path . 'SplClassLoader.php';
$classLoader = new SplClassLoader('Asar', $lib_path . 'asar');
$classLoader->setNamespaceSeparator('_');
$classLoader->register();

require_once $lib_path . 'asar/Asar/EnvironmentScope.php';
require_once $lib_path . 'asar/Asar/Injector.php';

/*
 Temporarily include testing sources
*/
require_once $lib_path . 'dev/testing/Asar/TempFilesManager.php';
require_once $lib_path . 'dev/testing/Asar/TempFilesManager/Exception.php';
require_once $lib_path . 'dev/testing/Asar/TestServerManager.php';


if (!isset($_SESSION)) {
  $_SESSION = array();
}
$scope = new Asar_EnvironmentScope(
  $_SERVER, $_GET, $_POST, $_FILES, $_SESSION, $_COOKIE, $_ENV, getcwd()
);
Asar_Injector::injectEnvironmentHelperBootstrap($scope)->run();
