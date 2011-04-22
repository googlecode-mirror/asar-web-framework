<?php
namespace Asar\Configurable;

/**
 * An interface that allows classes to be configurable
 */
interface ConfigurableInterface {
  function setConfig($key, $value);
  function getConfig($key);
}
