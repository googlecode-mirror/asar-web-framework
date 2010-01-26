<?php

class Asar_Utility_XML extends SimpleXMLElement {
  public function stringValue() {
    $name = $this->getName();
    $pattern = '/<' . $name . '[^>]*>(.*)<\/' . $name . '>/';
    preg_match($pattern, $this->asXML(), $matches);
    return $matches[1];
  }
  
  function getElementById($id) {
    $result = $this->xpath("//*[@id='$id']");
    if (count($result) === 0) {
      return null;
    }
    return $result[0];
  }
}
