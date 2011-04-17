<?php
namespace Asar\Router;

/**
 * @package Asar
 * @subpackage core
 */
interface RouterInterface {
  function route($app_name, $path, $map);
}
