<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_TemplateLocator {
  
  private 
    $app_path,
    $representation_path,
    $content_negotiator,
    $engine_extensions,
    $mime_types = array(
      'application/xhtml+xml' => 'xhtml',
      'text/html' => 'html',
      'application/xml' => 'xml',
      'application/json' => 'json',
      'text/javascript'  => 'js',
      'text/css' => 'css',
      'text/plain' => 'txt',
    );
  
  function __construct(
    Asar_ContentNegotiator_Interface $content_negotiator,
    $app_path, $engine_extensions
  ) {
    $this->content_negotiator = $content_negotiator;
    $this->app_path = $app_path;
    $this->engine_extensions = $engine_extensions;
    $this->representation_path = $this->constructPath(
      $this->app_path, 'Representation'
    );
  }
  
  function locateFor($resource_name, $request) {
    $rname = $this->getBaseResourceName($resource_name);
    $rpath = $this->constructPath($this->representation_path, $rname);
    $first_test_dir = substr_replace($rpath, '', strrpos($rpath, '/'));
    if (!is_dir($first_test_dir)) {
      return FALSE;
    }
    if (!is_dir($rpath)) {
      $res = $this->getTypesAndFiles(
        $first_test_dir, 
        "{$this->getResourceLastName($resource_name)}.{$request->getMethod()}",
        2
      );
    } else {
      $res = $this->getTypesAndFiles($rpath, $request->getMethod(), 1);
    }
    $type = $this->getType(
      $this->content_negotiator->negotiateFormat(
        $request->getHeader('Accept'), $res[0]
      )
    );
    if (!$type) {
      return FALSE;
    }
    foreach ($this->engine_extensions as $extension) {
      $suffix = "{$request->getMethod()}.$type.$extension";
      foreach ($res[1] as $file) {
        if (strpos($file, $suffix) !== FALSE) {
          return $this->constructPath($res[2], $file);
        }
      }
    }
    return FALSE;
  }
  
  function locateLayoutFor($template_file) {
    $file_part = explode(
      '.', substr($template_file, strrpos($template_file, '/') + 1)
    );
    $ext = array_pop($file_part);
    $file = $this->representation_path . DIRECTORY_SEPARATOR . 
      'Layout.' . array_pop($file_part) . '.' . $ext;
    if (file_exists($file)) {
      return $file;
    }
    return FALSE;
  }
  
  function getMimeTypeFor($template_file) {
    $len = strlen($template_file);
    $last_period = strrpos($template_file, '.');
    $last2_period = strrpos($template_file, '.', $last_period - $len - 1);
    $type = substr(
      $template_file,
      $last2_period + 1,
      $last_period - $last2_period - 1
    );
    return $this->getMimeType($type);
  }
  
  private function getTypesAndFiles($dir, $test_name, $type_pos) {
    $template_files = scandir($dir);
    $available_types = array();
    $available_files = array();
    foreach ($template_files as $file) {
      if ($file == '.' || $file == '..') {
        continue;
      }
      if (strpos($file, $test_name) === 0) {
        $file_exploded = explode('.', $file);
        $available_types[] = $this->getMimeType($file_exploded[$type_pos]);
        $available_files[] = $file;
      }
    }
    $available_types = $this->sortTypesOrder($available_types);
    
    return array($available_types, $available_files, $dir);
  }
  
  private function sortTypesOrder($available_types) {
    $preferred_order = array_keys($this->mime_types);
    $new_types_list = array();
    foreach ($preferred_order as $btype) {
      if (in_array($btype, $available_types)) {
        $new_types_list[] = $btype;
      }
    }
    return $new_types_list;
  }
  
  private function getMimeType($type) {
    return array_search($type, $this->mime_types);
  }
  
  private function constructFileName() {
    $args = func_get_args();
    return implode('.', $args);
  }
  
  private function constructPath() {
    $args = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $args);
  }
  
  private function getBaseResourceName($resource_name) {
    return str_replace(
      '_', DIRECTORY_SEPARATOR, 
      substr($resource_name, strpos($resource_name, '_Resource_') + 10)
    );
  }
  
  private function getResourceLastName($resource_name) {
    return substr($resource_name, strrpos($resource_name, '_') + 1);
  }
  
  private function getType($mime_type) {
    if (
      is_string($mime_type) && 
      array_key_exists($mime_type, $this->mime_types)
    ) {
      return $this->mime_types[$mime_type];
    }
    return FALSE;
  }
}
