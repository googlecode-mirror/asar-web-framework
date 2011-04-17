<?php
namespace Asar;

use \Asar\Response;

/**
 * @package Asar
 * @subpackage core
 */
class ResponseExporter {
  
  function exportResponse(Response $response) {
    $headers = $response->getHeaders();
    foreach ($headers as $key => $value) {
      $this->header("$key: $value");
    }
    $this->header(
      "HTTP/1.1 {$response->getStatus()} {$response->getStatusReasonPhrase()}"
    );
    echo $response->getContent();
  }
  
  function header($header_value) {
    @header($header_value);
  }
  
}
