<?php
interface Asar_Response_Interface
{
    function setContent($content);
    function getContent();
    function setStatus($status);
    function getStatus();
    function setHeader($name, $value);
    function getHeader($name);
    function setHeaders(array $headers);
    function getHeaders();
}
