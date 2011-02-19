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
    $v = $this->el(
      'div',
      $this->el(
        'table', $this->el('tbody', $this->createDebugTableData($debug))
      ),
      array('id' => 'asarwf_debug_info')
    );
    return $v;
  }
  
  private function el($name, $value = '', array $attributes = array()) {
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
      $data .= $this->el('tr', 
        $this->el('th', $name, 
          array('scope' => 'row', 'id' => 'asarwf_dbgl_' . $id)
        ) .
        $this->el('td', $this->createDataValues($value), 
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
      $items .= $this->el('li', $this->createDataValues($data));
    }
    return $this->el('ul', $items);
  }
  
  private function createDataTableValues($value) {
    $items = '';
    foreach ($value as $key => $data) {
      $items .= $this->el(
        'tr',
        $this->el('th', $key, array('scope' => 'row')) .
        $this->el('td', $this->createDataValues($data))
      );
    }
    return $this->el('table', $this->el('tbody', $items));
  }
  
  private function isAssociativeArray($array) {
    return $array !== array_values($array);
  }
  
}
