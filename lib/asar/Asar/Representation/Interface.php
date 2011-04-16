<?php
/**
 * @package Asar
 * @subpackage core
 */
interface Asar_Representation_Interface {
  function fetch($data, $method, array $options = array());
}
