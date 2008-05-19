<?php

class App_Controller_Errors extends Asar_Controller 
{
    
    /**
     * GET method
     *
     * @return void
     **/
    public function GET()
    {
        $this->response->setStatus(500);
    }
}