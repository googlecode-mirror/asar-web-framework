<?php

class Asar_TestServerManager {
  
  private static $can_connect_to_test_server;
  private $test_data_path;
  
  function __construct($test_data_path) {
    $this->test_data_path = $test_data_path;
  }
  
  private function constructPath() {
    $args = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $args);
  }
  
  private function constructRealPath() {
    $args = func_get_args();
    return realpath(
      call_user_func_array(array($this, 'constructPath'), $args)
    );
  }
  
  private function absoluteToRelative($from, $to) {
    $from_arr = explode(DIRECTORY_SEPARATOR, $from);
    $to_arr = explode(DIRECTORY_SEPARATOR, $to);
    $similarity = array();
    $len = count($from_arr);
    $n = 0;
    //echo "\n", $from, "\n", $to, "\n";
    while (count($from_arr) !== 0 && $n != 1000) {
      
      if ($from_arr[0] == $to_arr[0]) {
        $similarity[] = array_shift($from_arr);
        array_shift($to_arr);
      } else {
        break;
      }
      $n++;
    }
    $result_path = '';
    for ($i = 0; $i < count($from_arr) - 1; $i++) {
      $result_path .= '..' . DIRECTORY_SEPARATOR;
    }
    $result_path .= implode(DIRECTORY_SEPARATOR, $to_arr);
    return array(
      'base_path' => implode(DIRECTORY_SEPARATOR, $similarity),
      'from' => implode(DIRECTORY_SEPARATOR, $from_arr),
      'to' => $result_path
    );
  }
  
  function setUp($options) {
    $test_server_path = $this->constructPath(
      $this->test_data_path, 'test-server' 
    );
    
    $this->deleteIf($test_server_path);
    
    if (array_key_exists('fixture', $options)) {
      $server_dir = $this->constructRealPath(
        $this->test_data_path, 'test-server-fixtures', $options['fixture']
      );
    } else {
      $server_dir = realpath($options['path']);
    }
    symlink($server_dir, $test_server_path);
    if ($server_dir !== realpath($test_server_path)) {
      $this->deleteIf($test_server_path);
      $paths = $this->absoluteToRelative($test_server_path, $server_dir);
      chdir($paths['base_path']);
      $command = 'cd ' . escapeshellarg($paths['base_path']) . " ; " .
        'ln -s ' . escapeshellarg($paths['to']) . ' ' . 
        escapeshellarg($paths['from']);
      exec($command);
    }
  }
  
  // TODO: create test code for coverage's sake
  // Copied from Asar_Unit_ClientHttpTest
  function isCanConnectToTestServer() {
    if (is_null(self::$can_connect_to_test_server)) {
      self::$can_connect_to_test_server = false;
      $this->setUp(array('fixture' => 'normal'));
      $fp = @fsockopen('asar-test.local', 80, $errno, $errstr, 30);
      if (!$fp) {
        self::$can_connect_to_test_server = false;
      } else {
        $out = "GET / HTTP/1.0\r\n";
        $out .= "Host: asar-test.local\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        $test = stream_get_contents($fp);
        if (strpos($test, '<h1>This is the Great HTML</h1>') > 0) {
          self::$can_connect_to_test_server = true;
        } else {
          self::$can_connect_to_test_server = false;
        }
        fclose($fp);
      }
    }
    return self::$can_connect_to_test_server;
  }
  
  private function deleteIf($path) {
    if (is_link($path) || file_exists(realpath($path))) {
      unlink($path);
    }
  }
  
  function clearTestServer() {
    $test_server_path = $this->constructPath(
      $this->test_data_path, 'test-server' 
    );
    $this->deleteIf($test_server_path);
  }
}
