<?php
namespace Asar\Resource;
use \Asar\Request\RequestInterface;
/**
 * @package Asar
 * @subpackage core
 */
interface ResourceInterface {
  function handleRequest(RequestInterface $request);
}
