<?php
namespace Asar\Configurable;

/**
 * An interface that makes classes configurable
 *
 * @package Asar
 * @subpackage core
 */
interface ConfigurableInterface {
  function setConfig($key, $value);
  function getConfig($key);
}
