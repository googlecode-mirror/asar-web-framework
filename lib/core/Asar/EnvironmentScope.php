<?php

class Asar_EnvironmentScope {
  
  private
    $server  = array(),
    $get     = array(),
    $post    = array(),
    $files   = array(),
    $session = array(),
    $cookie  = array(),
    $env     = array(),
    $cwd,
    $argv    = array();
  
  function __construct(
    $server, $get, $post, $files, $session, $cookie, $env, $cwd, $argv = array()
  ) {
    $this->server  = $server;
    $this->get     = $get;
    $this->post    = $post;
    $this->files   = $files;
    $this->session = $session;
    $this->cookie  = $cookie;
    $this->env     = $env;
    $this->cwd     = $cwd;
    $this->argv    = $argv;
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
  
  function getArgv() {
    return $this->argv;
  }
  
  function getCurrentWorkingDirectory() {
    return $this->cwd;
  }
  
}
