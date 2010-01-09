<?php
set_include_path(
  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .
  realpath(dirname(__FILE__) . '/../lib/vendor') . PATH_SEPARATOR .
  get_include_path()
);
require_once 'Asar.php';
Asar::start('DummyApp');
