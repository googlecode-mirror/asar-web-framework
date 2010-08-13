<?php
interface Asar_Request_Interface
{
    function setPath($path);
    function getPath();
    function setMethod($method);
    function getMethod();
    function setHeader($name, $value);
    function getHeader($name);
    function setHeaders(array $headers);
    function getHeaders();
}
