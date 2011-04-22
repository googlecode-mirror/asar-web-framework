<?php
namespace Asar\ResponseFilter;

use \Asar\Response\ResponseInterface;
/**
 */
interface ResponseFilterInterface {
  function filterResponse(ResponseInterface $response);
}
