<?php
namespace Asar;

/**
 */
class EnvironmentScope {
  
  private
    $server  = array(),
    $get     = array(),
    $post    = array(),
    $files   = array(),
    $session = array(),
    $cookie  = array(),
    $env     = array();
  
  function __construct(
    $server, $get, $post, $files, $session, $cookie, $env
  ) {
    $this->server  = $server;
    $this->get     = $get;
    $this->post    = $post;
    $this->files   = $files;
    $this->session = $session;
    $this->cookie  = $cookie;
    $this->env     = $env;
  }
  
  function getServerVars() {
    return $this->server;
  }
  
  function getGetVars() {
    return $this->get;
  }
  
  function getPostVars() {
    return $this->post;
  }
  
  function getFilesVars() {
    return $this->files;
  }
  
  function getSessionVars() {
    return $this->session;
  }
  
  function getCookieVars() {
    return $this->cookie;
  }
  
  function getEnvVars() {
    return $this->env;
  }
}
