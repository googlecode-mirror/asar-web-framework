<?php


class Asar_IncludePathManagerTest extends PHPUnit_Framework_TestCase {
  
  private $old_include_path;
  
  function setUp() {
    if (!$this->old_include_path) {
      $this->old_include_path = get_include_path();
    }
    $this->resetIncludePath();
    $this->IPM = new Asar_IncludePathManager();
  }
  
  function tearDown() {
    $this->resetIncludePath();
  }
  
  private function resetIncludePath() {
    set_include_path($this->old_include_path);
  }
  
  function testAddingAnIncludePath() {
    $path_to_add = '/foo/bar';
    $this->assertNotContains($path_to_add, explode(PATH_SEPARATOR, get_include_path()));
    $this->IPM->add($path_to_add);
    $this->assertContains($path_to_add, explode(PATH_SEPARATOR, get_include_path()));
  }
  
  function testAddingAnIncludePathDoesNotAddAgain() {
    $path_to_add = '/foo/bar';
    $this->IPM->add($path_to_add);
    $this->IPM->add($path_to_add);
    $this->assertEquals(1, substr_count(get_include_path(), $path_to_add));
  }
  
  function testAddingUniquePathsAddThemToIncludePath() {
    $path_to_add1 = '/foo/bar';
    $path_to_add2 = '/foo/baz';
    $this->IPM->add($path_to_add1);
    $this->IPM->add($path_to_add2);
    $inc_path_array = explode(PATH_SEPARATOR, get_include_path());
    $this->assertContains($path_to_add1, $inc_path_array);
    $this->assertContains($path_to_add2, $inc_path_array);
  }
  
  function testCatchPossibleProblemWithOverlappingPathNames() {
    $path_to_add1 = '/foo/bar';
    $path_to_add2 = '/foo/ba';
    $this->IPM->add($path_to_add1);
    $this->IPM->add($path_to_add2);
    $inc_path_array = explode(PATH_SEPARATOR, get_include_path());
    $this->assertContains($path_to_add1, $inc_path_array);
    $this->assertContains($path_to_add2, $inc_path_array);
  }
  
  function testMutlipleAddPath() {
    $path_to_add1 = '/foo/bar';
    $path_to_add2 = '/foo/baz';
    $this->IPM->add(array($path_to_add1, $path_to_add2));
    $inc_path_array = explode(PATH_SEPARATOR, get_include_path());
    $this->assertContains($path_to_add1, $inc_path_array);
    $this->assertContains($path_to_add2, $inc_path_array);
  }
  
  function testMutlipleAddPath2() {
    $path_to_add1 = '/foo/bar';
    $path_to_add2 = '/foo/baz';
    $this->IPM->add($path_to_add1, $path_to_add2);
    $inc_path_array = explode(PATH_SEPARATOR, get_include_path());
    $this->assertContains($path_to_add1, $inc_path_array);
    $this->assertContains($path_to_add2, $inc_path_array);
  }
  
}
