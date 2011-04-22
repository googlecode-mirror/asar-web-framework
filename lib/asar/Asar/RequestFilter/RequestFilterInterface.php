<?php
namespace Asar\RequestFilter;

use \Asar\Request\RequestInterface;
/**
 */
interface RequestFilterInterface {
  function filterRequest(RequestInterface $request);
}
