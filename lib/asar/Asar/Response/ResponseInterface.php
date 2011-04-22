<?php
namespace Asar\Response;

use \Asar\Message\MessageInterface;

/**
 */
interface ResponseInterface extends MessageInterface {
  function setStatus($status);
  function getStatus();
}
