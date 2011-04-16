<?php
/**
 * @package Asar
 * @subpackage core
 */
interface Asar_Request_Interface extends Asar_Message_Interface {
  function setParams(array $params);
  function getParams();
  function setPath($path);
  function getPath();
  function setMethod($method);
  function getMethod();
}
