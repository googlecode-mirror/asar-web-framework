<?php
namespace Asar\ResponseFilter;

use \Asar\Response\ResponseInterface;
/**
 * @package Asar
 * @subpackage core
 */
interface ResponseFilterInterface {
  function filterResponse(ResponseInterface $response);
}
