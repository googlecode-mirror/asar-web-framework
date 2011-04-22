<?php
namespace Asar\Tests\Functional\DirectResourceMapping\Example1;
/**
 * Test Example1 Application Class
 * 
 * This is the Application definition for the application application
 * named 'Example1'. This test application is used only for integration
 * testing.
 */
class Config extends \Asar\Config {
  
  protected $config = array(
    'map' => array(
      '/what' => 'What'
    ),
  );

}
