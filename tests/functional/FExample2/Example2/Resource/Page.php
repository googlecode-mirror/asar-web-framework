<?php
class Example2_Resource_Page extends Asar_Resource
{
    public function GET()
    {
        $this->template->heading = "This is a test.";
    }
}

