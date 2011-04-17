<?php
namespace Asar\ResourceLister;

/**
 * @package Asar
 * @subpackage core
 */
interface ResourceListerInterface {
  function getResourceListFor($app_name);
}
