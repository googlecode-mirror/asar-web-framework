<?php
class Asar_Resource_Router {
  function getRoute($app, $path) {
    $app_name = str_replace('_Application', '', get_class($app));
    if ($path == '/') {
      $suffix = 'Index';
      return $app_name . '_Resource_' . $suffix;
    } else {
      $levels = explode('/', $path);
      array_shift($levels);
      $path_array = array();
      $name_now = $app_name . '_Resource';
      foreach($levels as $level) {
        $test_name = $name_now . '_' . Asar_Utility_String::camelCase($level);
        if (class_exists($test_name)) {
          $name_now = $test_name;
        } elseif(!class_exists($name_now . '__Item')) {
          return ' ';
        } else {
          $name_now .= '__Item';
        }
      }
      return $name_now;
    }
  }
}
