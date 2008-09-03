<?php

class Asar_Controller_Navigator extends Asar_Navigator {
    private $prefix = '';
    private static $navigators = array();
    
    static function getNavigator($context) {
        $prefix = Asar::getClassPrefix($context);
        if (array_key_exists($prefix, self::$navigators)) {
            return self::$navigators[$prefix];
        } else {
            self::$navigators[$prefix] = new self($prefix);
            return self::$navigators[$prefix];
        }
    }
    
    function __construct($prefix) {
        $this->prefix = $prefix;
    }
    
    function find($name) {
        return $this->prefix.'_Controller_'.$name;
    }
}