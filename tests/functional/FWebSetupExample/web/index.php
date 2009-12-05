<?php
// TODO: Use Asar_Utility_ServerSetup instead
set_include_path(
    realpath(dirname(__FILE__) .'/../../../../core') . PATH_SEPARATOR .
    realpath(dirname(__FILE__) . '/..') . PATH_SEPARATOR .
    get_include_path()
);
require_once 'Asar.php';
Asar::start('WebSetupExample');
