<?php
namespace Asar\Request;

use \Asar\Message\MessageInterface;
/**
 * @todo Move Request to Message namespace
 */
interface RequestInterface extends MessageInterface {
  function setParams(array $params);
  function getParams();
  function setPath($path);
  function getPath();
  function setMethod($method);
  function getMethod();
}
