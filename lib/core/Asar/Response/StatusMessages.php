<?php

class Asar_Response_StatusMessages 
  implements Asar_Response_StatusMessages_Interface {
  
  // TODO: These status messages should be moved to the configuration
  static protected $status_messages = array(
    404 => 'Sorry, we were unable to find the resource you were looking for (%s). Please check that you got the address or URL correctly. If that is the case, please email the administrator. Thank you and please forgive the inconvenience.',
    405 => 'The HTTP Method \'%s\' is not allowed for this resource.',
    406 => 'An appropriate representation of the requested resource could not be found.',
    500 => "The Server has encountered some problems.",
  );
  
  function getMessage(
    Asar_Response_Interface $response, Asar_Request_Interface $request
  ) {
    $methodName = 'get' . $response->getStatus() . 'Message';
    if (!method_exists($this, $methodName)) {
      return false;
    }
    return $this->getSummary($response) . call_user_func_array(
      array($this, $methodName), array($response, $request)
    );
  }
  
  private function getSummary($response) {
    return $response->getStatusReasonPhrase() . 
      " ({$response->getStatus()})\n\n";
  }
  
  private function get404Message($response, $request) {
    return sprintf(self::$status_messages[404], $request->getPath());
  }
  
  private function get405Message($response, $request) {
    return sprintf(self::$status_messages[405], $request->getMethod());
  }
  
  private function get406Message($response, $request) {
    return self::$status_messages[406];
  }
  
  private function get500Message($response, $request) {
    return self::$status_messages[500] . "\n" . $response->getContent();
  }
  
}
