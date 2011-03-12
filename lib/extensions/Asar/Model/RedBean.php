<?php

abstract class Asar_Model_RedBean {
  
  private $oodb, $bean;
  
  function __construct(
    RedBean_ObjectDatabase $oodb, RedBean_OODBBean $bean = NULL
  ) {
    $this->oodb = $oodb;
    if ($bean) {
      $this->bean = $bean;
    } else {
      $this->bean = $this->oodb->dispense(get_class($this));
    }
    $this->defineProperties();
  }
  
  function save() {
    $this->oodb->store($this->bean);
  }
  
  function defineProperties() {}
  
}
