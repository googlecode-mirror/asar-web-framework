<?php
class ResourceTraversing_Config implements Asar_Config_Interface {
  
  function getConfig($key = null) {
    return array(
      'use_templates' => false,
      'map' => array(
        
      ),
      
    );
  }
  
  function importConfig(Asar_Config_Interface $config) {}
  
}
