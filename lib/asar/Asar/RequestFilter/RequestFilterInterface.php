<?php
namespace Asar\RequestFilter;

use \Asar\Request\RequestInterface;
/**
 * @package Asar
 * @subpackage core
 */
interface RequestFilterInterface {
  function filterRequest(RequestInterface $request);
}
