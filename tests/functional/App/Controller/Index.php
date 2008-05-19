<?php
/**
 * App_Controller_Index, Root controller for test Application
 * 
 * @package functional_test_app
 **/
class App_Controller_Index extends Asar_Controller 
{
    protected $map = array(
		'subpage' => 'Subpage',
		'errors'  => 'Errors'
		);
    
    /**
     * GET method
     *
     * @return void
     **/
    public function GET()
    {
        $this->view['greeting'] = 'Hello world!';
    }
} // END class App_Controller_Index extends Asar_Controller
