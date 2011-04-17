<?php
ini_set('error_reporting', E_ALL | E_STRICT);

$lib_path = realpath(__DIR__ . '/../lib/') . '/';
require_once $lib_path . 'SplClassLoader.php';

$classLoader = new SplClassLoader('Asar\Tests', __DIR__);
$classLoader->register();

$classLoader = new SplClassLoader('Asar', $lib_path . 'asar');
$classLoader->register();

/*
 Temporarily include testing sources
*/
require_once $lib_path . 'dev/testing/Asar/TempFilesManager.php';
require_once $lib_path . 'dev/testing/Asar/TempFilesManager/Exception.php';
require_once $lib_path . 'dev/testing/Asar/TestServerManager.php';


if (!isset($_SESSION)) {
  $_SESSION = array();
}
$scope = new \Asar\EnvironmentScope(
  $_SERVER, $_GET, $_POST, $_FILES, $_SESSION, $_COOKIE, $_ENV, getcwd()
);
\Asar\Injector::injectEnvironmentHelperBootstrap($scope)->run();
