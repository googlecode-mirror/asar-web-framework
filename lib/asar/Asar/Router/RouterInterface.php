<?php
namespace Asar\Router;

/**
 */
interface RouterInterface {
  function route($app_name, $path, $map);
}
