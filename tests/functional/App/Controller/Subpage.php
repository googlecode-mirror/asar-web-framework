<?php
/**
 * App_Controller_Index, Root controller for test Application
 * 
 * @package functional_test_app
 **/
class App_Controller_Subpage extends Asar_Controller 
{
    
    /**
     * GET method
     *
     * @return void
     **/
    public function GET()
    {
        $this->view['heading'] = 'This is a test heading';
        $this->view['content'] = 'This is the content. It is longer than the title';
    }
} // END class App_Controller_Index extends Asar_Controller