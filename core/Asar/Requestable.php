<?php
interface Asar_Requestable {
    function handleRequest(Asar_Request $handler, array $arguments = NULL);
    //function processResponse(Asar_Response $response);
}
