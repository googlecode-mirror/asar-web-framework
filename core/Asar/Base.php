<?php

/**
 * @todo This class may be following the anti-pattern "BaseBean". Refactor to move methods in different classes?
 * 
 */
abstract class Asar_Base {
  
  
    public function exception($msg) {
        Asar::exception($this, $msg);
    }
  
    public function debug($title, $msg) {
        Asar::debug($title, $msg);
    }
    
    /**
     * See if the environment is in debug mode
     *
     * @return bool
     **/
    protected function isDebugMode()
    {
        return (Asar::getMode() == Asar::MODE_DEVELOPMENT);
    }

}

