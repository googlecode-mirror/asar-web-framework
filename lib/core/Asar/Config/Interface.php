<?php
/**
 * Provides a generic interface for configurations
 *
 * @package Asar
 * @subpackage core
 */
interface Asar_Config_Interface {
  
  /**
   * Returns the value of a configuration directive based on a key or
   * all the configuration values in a single multi-dimensional array
   * when no key is specified.
   *
   * @param string $key
   * @return mixed|array
   */
  function getConfig($key = null);
  
  /**
   * Imports another configuration's values. When importing, non-matching keys
   * from the imported configuration should be added to the original.
   * For matching keys, implementations of this interface should retain the
   * value from the original configuration.
   */
  function importConfig(Asar_Config_Interface $config);
}
