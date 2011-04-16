<?php
/**
 * An interface that makes classes configurable
 *
 * @package Asar
 * @subpackage core
 */
interface Asar_Configurable_Interface {
  function setConfig($key, $value);
  function getConfig($key);
}
