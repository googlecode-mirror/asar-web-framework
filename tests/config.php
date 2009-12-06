<?php

$core_path = realpath(dirname(__FILE__) . '/../core');
set_include_path( $core_path . PATH_SEPARATOR . get_include_path());
require_once 'Asar.php';
