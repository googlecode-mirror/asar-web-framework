<?php
/**
 * @package Asar
 * @subpackage core
 */
interface Asar_Response_Interface extends Asar_Message_Interface {
  function setStatus($status);
  function getStatus();
}
