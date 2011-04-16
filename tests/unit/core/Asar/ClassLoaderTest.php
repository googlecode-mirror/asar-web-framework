<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ClassLoaderTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->file_searcher = $this->getMock(
      'Asar_FileSearcher_Interface', array('find')
    );
    $this->include_manager = $this->getMock(
      'Asar_FileIncludeManager_Interface'
    );
    $this->CL = new Asar_ClassLoader(
      $this->file_searcher, $this->include_manager
    );
  }
  
  private function fileSearcherMockStartup() {
    return $this->file_searcher->expects($this->once())->method('find');
  }
  
  function testSimpleClassLoadingUnsuccessful() {
    $this->fileSearcherMockStartup()->will($this->returnValue(false));
    $this->assertFalse($this->CL->load('FooClass'));
  }
  
  function testSimpleClassLoadingSuccessful() {
    $this->fileSearcherMockStartup()->will($this->returnValue('a/path/value'));
    $this->assertTrue($this->CL->load('FooClass'));
  }
  
  function testClassLoaderPassesConvertedClassNameToFileSearcher() {
    $this->fileSearcherMockStartup()->with('FooClass.php');
    $this->CL->load('FooClass');
  }
  
  function testClassLoaderPassesConvertedUnderscoredClassNameToFileSearcher() {
    $this->fileSearcherMockStartup()->with('Foo/Bar/BazClass.php');
    $this->CL->load('Foo_Bar_BazClass');
  }
  
  function testClassLoaderIncludesPathPassedByFileSearcher() {
    $path = '/a/path/to/a/file';
    $this->fileSearcherMockStartup()->will($this->returnValue($path));
    $this->include_manager->expects($this->once())
      ->method('requireFileOnce')
      ->with($path);
    $this->CL->load('Foo/Class');
  }
  
  function testClassLoaderDoesNotIncludeWhenFindReturnsFalse() {
    $this->fileSearcherMockStartup()->will($this->returnValue(false));
    $this->include_manager->expects($this->never())
      ->method('requireFileOnce');
    $this->CL->load('Foo/Class');
  }
  
  function testGettingFileSearcher() {
    $this->assertSame($this->file_searcher, $this->CL->getSearcher());
  }
  
  function testGettingIncludeManager() {
    $this->assertSame($this->include_manager, $this->CL->getIncludeManager());
  }
}
