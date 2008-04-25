<?php

abstract class Asar_Request_Handler extends Asar_Base implements Asar_Requestable {
    
    final function handleRequest(Asar_Request $request, array $arguments = NULL) {
        if (!empty($this->request_filters)) {
            foreach($this->request_filters as $filter) {
                call_user_func( $filter, $request);
            }
        }
        $response = $this->processRequest($request, $arguments);
        if (!empty($this->response_filters)) {
            foreach($this->response_filters as $filter) {
                call_user_func($filter, $response);
            }
        }
        return $response;
    }
    
    final function getRequestFilters() {
        return $this->request_filters;
    }
    
    final function getResponseFilters() {
        return $this->response_filters;
    }
    
    abstract protected function processRequest(Asar_Request $request, array $arguments = NULL);
    
}