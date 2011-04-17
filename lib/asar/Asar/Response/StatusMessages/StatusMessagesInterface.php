<?php
namespace Asar\Response\StatusMessages;

use \Asar\Response\ResponseInterface;
use \Asar\Request\RequestInterface;

/**
 * @package Asar
 * @subpackage core
 */
interface StatusMessagesInterface {
  function getMessage(
    ResponseInterface $response, RequestInterface $request
  );
}
