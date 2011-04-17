<?php
namespace Asar\Response;

use \Asar\Message\MessageInterface;

/**
 * @package Asar
 * @subpackage core
 */
interface ResponseInterface extends MessageInterface {
  function setStatus($status);
  function getStatus();
}
