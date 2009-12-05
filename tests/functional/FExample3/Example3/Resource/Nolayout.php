<?php
class Example3_Resource_Nolayout extends Asar_Resource
{
    public function GET()
    {
        $this->template->noLayout();
        $this->template->h1 = 'This is the main heading.';
        $this->template->p  = 'This is the paragraph. Easy, no?';
    }
}

