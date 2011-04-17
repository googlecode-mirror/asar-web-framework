<?php
namespace Asar\Model;

/**
 * @todo Remove this from core library
 */
abstract class RedBean {
  
  private $oodb, $bean;
  
  function __construct(
    \RedBean_ObjectDatabase $oodb, \RedBean_OODBBean $bean = NULL
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
