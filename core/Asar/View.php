<?php

class Asar_View implements Asar_View_Interface {
    private $_template_file = null;
    private $_tpl;
    
    function __construct() {
        $this->_tpl = new Asar_Template;
    }
    
    function __set($variable, $value = null) {
        $this->set($variable, $value);
    }
    
    function set($variable, $value = null) {
        $this->_tpl->set($variable, $value);
    }
    
    function setTemplate($file) {
        $this->_tpl->setTemplate($file);
    }
    
    function render() {
        return $this->_tpl->fetch();
    }
    
}
