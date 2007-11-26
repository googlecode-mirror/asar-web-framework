<?php

require_once 'Asar.php';


Asar::start('testapp');
echo str_replace(':', '<br />', ini_get('include_path'));
?>