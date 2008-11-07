<?php

/* A sample interface for Asar_View */

class Sample_View_Object implements Asar_View {
    //..
}

$view = new Sample_View_Object;
$view->setTemplateFile('template.php');

$view->var_name = 'A variable name';

// Multiple Setting example
$view->set(
    'var1' => 'A value',
    'var2' => 'Another value',
    'var3' => 'Yet another value'
);
echo $view->var_name; // outputs 'A variable name'
echo $view->var1; // outputs 'A value'
$view->render(); // displays the template output (without echo);
$view->getTemplateFile(); // returns 'template.php'


