<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

class FAsarWFExample_Test extends Asar_Test_Helper {
  
  function setUp() {
    // This is so we put all created files in the temporary directory
    chdir(self::getTempDir());
    $this->asarwf = realpath(dirname(__FILE__) . '/../../../bin/asarwf');
    $this->current_path = dirname(__FILE__);
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
  
  protected function _matchPaths($basis, $test) {
    foreach (scandir($basis) as $file) {
      // Skip '.svn',  '.' and '..'
      if ($file == '.svn' || preg_match('/^.{1,2}$/', $file)) {
        continue;
      }
      
      $base_file = Asar::constructPath($basis, $file);
      $test_file = Asar::constructPath($test, $file);
      $this->assertFileExists($test_file);
      $this->assertEquals(
        str_replace("\n", '\n', file_get_contents($base_file)),
        str_replace("\n", '\n', file_get_contents($test_file)),
        "Failed matching contents of '$test_file'."
      );
      if (is_dir($base_file)) {
        $this->_matchPaths( $base_file, $test_file );
      }
    }
  }
  
  function testCreatingProject() {
    $this->execute('create-project dummy_project DummyApp');
    $this->_matchPaths(
      Asar::constructPath($this->current_path, 'base_project'),
      Asar::constructPath(self::getTempDir(), 'dummy_project')
    );
  }
  
  function testCreateResource() {
    $this->execute('create-project my-project MyApp');
    chdir('my-project');
    $this->execute('create-resource /foo');
    $this->assertFileExists('apps/MyApp/Resource/Foo.php');
    $this->assertContains(
      'class MyApp_Resource_Foo extends Asar_Resource {',
      file_get_contents('apps/MyApp/Resource/Foo.php')
    );
  }
  
  function testCreateMultiLevelResource() {
    $this->execute('create-project aproject AnApp');
    chdir('aproject');
    $this->execute('create-resource /foo/bar/baz');
    $files = array(
      'Foo.php'         => 'Foo',
      'Foo/Bar.php'     => 'Foo_Bar',
      'Foo/Bar/Baz.php' => 'Foo_Bar_Baz'
    );
    foreach ($files as $file => $name) {
      $this->assertFileExists('apps/AnApp/Resource/' . $file);
      $this->assertContains(
        "class AnApp_Resource_$name extends Asar_Resource {",
        file_get_contents('apps/AnApp/Resource/' . $file)
      );
    }
  }
  
}

