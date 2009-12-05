#!/usr/bin/php
<?php
echo stream_get_contents(STDOUT);
echo ' hello ';
function yoHandler($errno, $errstr) {
    echo 'Yesh Man!';
}

set_error_handler('yoHandler');
//%HEADER_TEST_CODE%//

header('Content-Type: text/plain');
header(2);

var_dump(stream_get_wrappers());
restore_error_handler();
$headers = headers_list();
var_dump($headers);
