<?php

class Asar_ContentNegotiator implements Asar_ContentNegotiator_Interface {
  
  function negotiateFormat($accept_header, array $available_formats) {
    if (empty($available_formats)) {
      throw new Asar_ContentNegotiator_Exception(
         'Asar_ContentNegotiator::negotiateFormat(). 2nd argument '.
          'must not be empty. Please specify available formats.'
      );
    }
    $accept_list = $this->getAcceptList($accept_header);
    foreach ($available_formats as $format) {
      if (!isset($preferred_format)) {
        $preferred_format = $format;
      }
      foreach ($accept_list as $q => $accepts) {
        if (preg_match('/^[\w+-_]+\/[\*]$/', $accepts) ) {
          $exp = str_replace('*', '', $accepts);
            if (strpos($format, $exp) === 0) {
              return $format;
            }
        }
        if ($accepts == $format) {
          return $format;
        }
      }
    }
    if ($accepts == '*/*') {
      return $preferred_format;
    }
    return FALSE;
  }
  
  function getAcceptList($accept_header) {
    $temp_list = explode(',', $accept_header);
    $array = array();
    foreach ($temp_list as $value) {
      if (strpos($value, ';') > 0) {
        $s = explode(';', $value);
        $array[$s[1]] = $s[0];
      } else {
        $array[] = $value;
      }
    }
    //var_dump($array);
    return $array;
  }
  
}
