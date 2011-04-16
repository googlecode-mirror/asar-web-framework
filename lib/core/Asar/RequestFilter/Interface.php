<?php
/**
 * @package Asar
 * @subpackage core
 */
interface Asar_RequestFilter_Interface {
  function filterRequest(Asar_Request_Interface $request);
}
