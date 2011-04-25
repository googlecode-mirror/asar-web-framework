<?php
ini_set('error_reporting', E_ALL | E_STRICT);

$lib_path = realpath(__DIR__ . '/../lib/') . '/';
require_once $lib_path . 'asar/Asar/ClassLoader.php';

$classLoader = new \Asar\ClassLoader('Asar\Tests', __DIR__);
$classLoader->register();
$classLoader = new \Asar\ClassLoader('Asar', $lib_path . 'asar');
$classLoader->register();

if (!isset($_SESSION)) {
  $_SESSION = array();
}
$scope = new \Asar\EnvironmentScope(
  $_SERVER, $_GET, $_POST, $_FILES, $_SESSION, $_COOKIE, $_ENV, getcwd()
);
