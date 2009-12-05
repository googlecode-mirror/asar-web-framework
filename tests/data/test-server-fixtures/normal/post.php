<?php
var_dump($_POST);
var_dump($_SERVER);
var_dump($_ENV);
var_dump(
file_get_contents('php://input')
);
