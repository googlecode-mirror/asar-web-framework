<?php
namespace Asar\Response\StatusMessages;

use \Asar\Response\ResponseInterface;
use \Asar\Request\RequestInterface;

/**
 */
interface StatusMessagesInterface {
  function getMessage(
    ResponseInterface $response, RequestInterface $request
  );
}
