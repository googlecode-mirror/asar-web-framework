<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

class FAsarWFExample_Test extends Asar_Test_Helper {
  
  function setUp() {
    // This is so we put all created files in the temporary directory
    chdir(self::getTempDir());
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
    $expected_files = array(
      'dummy_project/apps/DummyApp/Application.php',
      'dummy_project/lib/vendor',
      'dummy_project/tests/data',
      'dummy_project/tests/functional',
      'dummy_project/tests/unit',
      'dummy_project/apps/DummyApp/Resource/Index.php',
      'dummy_project/web/index.php',
      'dummy_project/web/.htaccess',
      'dummy_project/tasks.php'
    );
    foreach ($expected_files as $file) {
      $this->assertFileExists($file);
    }
    
    $index_contents = 
      "<?php\n" .
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
    $htaccess_contents = "<IfModule mod_rewrite.c>\n" .
      "RewriteEngine On\n".
      "RewriteBase /\n".
      "RewriteCond %{REQUEST_FILENAME} !-f\n".
      "RewriteCond %{REQUEST_FILENAME} !-d\n".
      "RewriteRule . /index.php [L]\n".
      "</IfModule>\n";
    $this->assertEquals(
      $htaccess_contents, file_get_contents('dummy_project/web/.htaccess')
    );
    
    $task_file_contents = file_get_contents('dummy_project/tasks.php');
    $this->assertContains("<?php\n", $task_file_contents);
    $this->assertContains(
      '$main_app = \'DummyApp\';' . "\n", $task_file_contents
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
