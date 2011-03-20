<?php

class Asar_DebugPrinter_Html implements Asar_DebugPrinter_Interface {
  
  private $block_elements = array('div', 'table', 'tbody', 'tr');
  
  function printDebug(Asar_Debug $debug, $content) {
    if (strpos($content, '</body>')) {
      return str_replace(
        '</body>', $this->createDebugContent($debug) . "\n</body>", $content
      );
    }
    return $content . $this->createDebugContent($debug);
  }
  
  private function createDebugContent($debug) {
    return $this->element(
      'div',
      $this->element(
        'table', $this->element('tbody', $this->createDebugTableData($debug))
      ),
      array('id' => 'asarwf_debug_info')
    );
  }
  
  private function element($name, $value = '', array $attributes = array()) {
    $attributes_list = '';
    foreach ($attributes as $attr_name => $attr_value) {
      $attributes_list .= " $attr_name=\"$attr_value\"";
    }
    return "<$name" . $attributes_list . ">$value</$name>";
  }
  
  private function createDebugTableData($debug) {
    $data = '';
    foreach ($debug as $name => $value) {
      $id = Asar_Utility_String::underScore($name);
      $data .= $this->element(
        'tr', 
        $this->element(
          'th', $name, array('scope' => 'row', 'id' => 'asarwf_dbgl_' . $id)
        ) .
        $this->element(
          'td', $this->createDataValues($value), 
          array('id' => 'asarwf_dbgv_' . $id)
        )
      );
    }
    return $data;
  }
  
  private function createDataValues($value) {
    if (is_array($value)) {
      if ($this->isAssociativeArray($value)) {
        return $this->createDataTableValues($value);
      }
      return $this->createDataListValues($value);
    }
    return $value;
  }
  
  private function createDataListValues($value) {
    $items = '';
    foreach ($value as $data) {
      $items .= $this->element('li', $this->createDataValues($data));
    }
    return $this->element('ul', $items);
  }
  
  private function createDataTableValues($value) {
    $items = '';
    foreach ($value as $key => $data) {
      $items .= $this->element(
        'tr',
        $this->element('th', $key, array('scope' => 'row')) .
        $this->element('td', $this->createDataValues($data))
      );
    }
    return $this->element('table', $this->element('tbody', $items));
  }
  
  private function isAssociativeArray($array) {
    return $array !== array_values($array);
  }
  
}
