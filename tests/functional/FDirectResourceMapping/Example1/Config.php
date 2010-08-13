<?php
/**
 * Test Example1 Application Class
 * 
 * This is the Application definition for the application application
 * named 'Example1'. This test application is used only for integration
 * testing.
 */
class Example1_Config implements Asar_Config_Interface {
  
  function getConfig($key = null) {
    return array(
      
      'map' => array(
        '/'     => 'Index',
        '/what' => 'What'
      ),
      
    );
  }
  
  function importConfig(Asar_Config_Interface $config) {}
  
}
