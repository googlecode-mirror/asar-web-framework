<?php
class Example3_Resource_SetLayout extends Asar_Resource
{
    public function GET()
    {
        $this->template->p  = 'This is the paragraph from SetLayout.php';
        $this->template->getLayout()->title = 'SetLayout Title';
    }
    
}

