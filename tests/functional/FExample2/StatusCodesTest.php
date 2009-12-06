<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FExample2_StatusCodesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Asar_Client;
        $this->app = new Example2_Application;
        $this->client->setServer($this->app);
    }
    
    // 2xx Successful Tests
    
    public function testStatus200()
    {
        $this->assertEquals(
            200, $this->client->GET('/')->getStatus(),
            'The Http Status code is not 200 for Okay responses.'
        );
    }
    /*
    public function testStatus201()
    {
        // "Created"
        $this->markTestIncomplete(
            "Test for Status Code 201 'The request has been fulfilled and ".
            "resulted in a new source being created' is not ready yet"
        );
    }*/
    
    function testStatus404()
	{
		$response = $this->client->GET('/non-existent-path');
		$this->assertEquals(
			404, $response->getStatus(),
			'Application did not return a 404 Response Status when '.
			'requesting a non-existent resource.'
		);
		$this->assertContains(
			'File Not Found', $response->getContent(),
			'Application response did not say what type of error it was for 404'
		);
		$this->assertContains(
		    'Sorry, we were unable to find the resource you were looking for. '.
		    	'Please check that you got the address or URL correctly. If '.
		    	'that is the case, please email the administrator. Thank you '.
		    	'and please forgive the inconvenience.',
			$response->getContent(),
			'Application did not return a proper 404 message'
		);
	}
	
	function testStatus405()
	{
		// TODO: Test sending allowed methods
		$response = $this->client->POST('/');
		$this->assertEquals(
			405, $response->getStatus(),
			'Application did not return a 405 Response Status when '.
			'requesting a non-existent resource.'
		);
		$this->assertContains(
			'Method Not Allowed (405)', $response->getContent(),
			'Application response did not say what type of error it was for 405'
		);
		$this->assertContains(
		    'The HTTP Method \'POST\' is not allowed for this resource.',
			$response->getContent(),
			'Application did not return a proper 405 message'
		);
	}
	
	public function testStatus406($mimetype = 'unknown/mime-type') {
		$response = $this->client->GET('/page', array(), 
		  array('Accept' => $mimetype));
		$this->assertEquals(
			406, $response->getStatus(),
			'Application did not return a 406 Response Status when ' .
			'requesting a representation for a resource that the server ' .
			'does not recognize.'
		);
		$this->assertContains(
			'Not Acceptable (406)', $response->getContent(),
			'Application response did not say what type of error it was for 406'
		);
		$this->assertContains(
		  'An appropriate representation of the requested resource could not be found.',
			$response->getContent(),
			'Application did not return a proper 406 message'
		);
	}
	
	public function testStatus406B() {
    $this->testStatus406('application/xml');
	}

  public function testStatus500() {
    $response = $this->client->GET('/500');
    $content = $response->getContent();
    $this->assertEquals(
    	500, $response->getStatus(),
    	'The Application did not return a 500 Response Status '.
    	'when the resource throws an Exception'
    );
    $this->assertContains(
	    'Internal Server Error (500)', $content,
	    'Application response did not say what type of error it was for 500'
    );
    $this->assertContains(
	    'The Server has encountered some problems.', $content,
	    'Application did not return a proper 500 message.'
    );
    $this->assertContains(
      'Something is wrong.', $content,
      'Application did not return the exception message.'
    );
  }

}

