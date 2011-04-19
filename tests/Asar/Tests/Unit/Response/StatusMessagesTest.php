<?php

namespace Asar\Tests\Unit\Response;

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use \Asar\Response\StatusMessages;
use \Asar\Request;
use \Asar\Response;

class StatusMessagesTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->SM = new StatusMessages;
  }
  
  /**
   * @dataProvider statusMessages
   */ 
  function testStatusMessages(
    $status, $full_message, $path = '/', $method = 'GET', $response_content = ''
  ) {
    $request = new Request(array(
      'path' => $path, 'method' => $method
    ));
    $response = new Response(array('status' => $status));
    $response->setContent($response_content);
    $msg = $this->SM->getMessage($response, $request);
    $this->assertContains(
      //TODO: Make this more literal
      $response->getStatusReasonPhrase() . " ($status)",
      $msg,
      "Did not find status summary in $msg."
    );
    $this->assertContains( 
      $full_message, $msg,
      "$msg does not contain required full message."
    );
  }
  
  function statusMessages() {
    return array_merge(
      array(
        array(
          406,
          'An appropriate representation of the requested ' .
            'resource could not be found.'
        ),
        array(
          405,
          'The HTTP Method \'POST\' is not allowed for this resource.',
          '/', 'POST'
        ),
        array(
          500,
          "The Server has encountered some problems.\nFoo Bar error message.",
          '/', 'GET', 'Foo Bar error message.'
        ),
        array(
          404,
          'Sorry, we were unable to find the resource ' .
            'you were looking for (/unknown/path). '.
            'Please check that you got the address or URL correctly. If '.
            'that is the case, please email the administrator. Thank you '.
            'and please forgive the inconvenience.',
          '/unknown/path'
        )
      )
      //$this->status405Messages()
    );
  }
  
  function testStatusMessagesReturnsFalseForUnknwonStatus() {
    $request = new Request;
    $response = new Response(array('status' => 1));
    $this->assertFalse($this->SM->getMessage($response, $request));
  }
  
  function testStatusMessage500Sureness() {
    $request = new Request;
    $response = new Response(array('status' => 500));
    $response->setContent('The error message');
    $msg = $this->SM->getMessage($response, $request);
    $this->assertContains( 
      "The Server has encountered some problems.\nThe error message", $msg,
      "$msg does not contain required full message."
    );
  }
}
