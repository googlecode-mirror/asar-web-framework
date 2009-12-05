<?php
class Example3_Resource_Xml extends Asar_Resource
{
    public function GET()
    {
        $this->template->foo  = 'This is from Xml.php';
    }
    
}

