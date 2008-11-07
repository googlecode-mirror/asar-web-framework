<?php

class Asar_Controller_Locator extends Asar_Locator {
    private $prefix = '';
    private static $locators = array();
    
    static function getLocator($context) {
        $prefix = Asar::getClassPrefix($context);
        if (array_key_exists($prefix, self::$locators)) {
            return self::$locators[$prefix];
        } else {
            self::$locators[$prefix] = new self($prefix);
            return self::$locators[$prefix];
        }
    }
    
    function __construct($prefix) {
        $this->prefix = $prefix;
    }
    
    function find($name) {
        return $this->prefix.'_Controller_'.$name;
    }
}
