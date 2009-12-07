<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

class FAsarWFExample_Test extends PHPUnit_Framework_TestCase {
  function setUp() {
    $this->asarwf = realpath(dirname(__FILE__) . '/../../../bin/asarwf');
  }
  
  private function execute($command) {
    $output = array();
    exec($this->asarwf . " $command", $output);
    return $output;
  }
  
  function testGettingVersion() {
    $this->assertContains(
      'Asar Web Framework ' . Asar::getVersion(),
      $this->execute('--version')
    );
  }
  
  function testCreatingProject() {
    
    $this->execute('create-project dummy_project DummyApp');
    
    $this->assertFileExists(
      'dummy_project/apps/DummyApp/Application.php'
    );
    $this->assertFileExists(
      'dummy_project/vendor'
    );
    $this->assertFileExists(
      'dummy_project/apps/DummyApp/Resource/Index.php'
    );
    $this->assertFileExists(
      'dummy_project/web/index.php'
    );
    $index_contents = 
      "set_include_path(\n" .
      "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n" .
      "  realpath(dirname(__FILE__) . '/../vendor') . PATH_SEPARATOR .\n" .
      "  get_include_path()\n" .
      ");\n" .
      "require_once 'Asar.php';\n" .
      "Asar::start('DummyApp');\n";
    $this->assertEquals(
      $index_contents, file_get_contents('dummy_project/web/index.php')
    );
    $this->assertFileExists(
      'dummy_project/web/.htaccess'
    );
    $htaccess_contents = "<IfModule mod_rewrite.c>\n" .
      "RewriteEngine On\n".
      "RewriteBase /\n".
      "RewriteCond %{REQUEST_FILENAME} !-f\n".
      "RewriteCond %{REQUEST_FILENAME} !-d\n".
      "RewriteRule . /index.php [L]\n".
      "</IfModule>";
    $this->assertEquals(
      $htaccess_contents, file_get_contents('dummy_project/web/.htaccess')
    );
    rmdir('dummy_project');
  }
  
  
}
