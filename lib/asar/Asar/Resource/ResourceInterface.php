<?php
namespace Asar\Resource;
use \Asar\Request\RequestInterface;
/**
 */
interface ResourceInterface {
  function handleRequest(RequestInterface $request);
}
