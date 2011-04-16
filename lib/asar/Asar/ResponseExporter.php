<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_ResponseExporter {
  
  function exportResponse(Asar_Response $response) {
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
