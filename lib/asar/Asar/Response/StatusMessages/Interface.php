<?php
/**
 * @package Asar
 * @subpackage core
 */
interface Asar_Response_StatusMessages_Interface {
  function getMessage(
    Asar_Response_Interface $response, Asar_Request_Interface $request
  );
}
