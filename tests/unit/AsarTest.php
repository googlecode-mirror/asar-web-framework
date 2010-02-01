<?php
require_once realpath(dirname(__FILE__). '/../config.php');
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Parent_Class {
  function throwException() {
    Asar::exception($this, 'Throwing exception for '.get_class($this));
  }
}

class Test_Child_Class extends Test_Parent_Class {
  private $attr1 = '';
  private $attr2 = '';
  
  function __construct($arg1 = NULL, $arg2 = NULL) {
    $this->attr1 = $arg1;
    $this->attr2 = $arg2;
  }
  
  function getAttr1() {
    return $this->attr1;
  }
  
  function getAttr2() {
    return $this->attr2;
  }
}
class Test_2Child_Class extends Test_Parent_Class {}
class Test_GrandChild_Class extends Test_Child_Class {}
class Test_Parent_Class_Exception extends Exception {}
class Test_Child_Class_Exception extends Test_Parent_Class_Exception {} 
class Test_Class_With_No_Exception {
  function throwException() {
    Asar::exception($this, 'Throwing exception for '.get_class($this));
  }
}

class AsarTest_Application extends Asar_Application {}
class AsarTest_Controller_Index extends Asar_Resource{
  function GET() {
    return 'Hello World';
  }
}

class Test2_Class {}
class TestClassWithNoPrefix {}

abstract class Uninstantiable_Class {}





class AsarTest extends Asar_Test_Helper {
  
  protected function setUp() {
    $_SERVER = array();
    $_SERVER['SERVER_NAME'] = 'sample.website';
    Asar::setInterpreter(null);
  }
  
  protected function tearDown() {
    //Asar::reset();
  }
  
  function testGetVersion() {
    $this->assertEquals(
      Asar::getVersion(), '0.3', 
      'Unable to get proper version'
    );
  }
  
  // @todo Create test for the part of the code that includes the file
  function testLoadClass() {
    $this->assertFalse(
      Asar::loadClass('Test_Dummy_Class'),
      'Did not attempt to load the class definition'
    );
  }
  
  function testLoadingExistingClass() {
    $class_name = 'Temp_Class_Test';  
    
    // Test first if class exists. It must not.
    $this->assertFalse(class_exists($class_name, false), 'Class definition was loaded already!');
    
    // Create a file that follows naming convention
    chdir(dirname(__FILE__)); // Fixes getcwd problems. 
    if (!file_exists('Temp/Class')) {
      mkdir('Temp', 0777, true);
      mkdir('Temp/Class', 0777, true);
    }
    $fname = 'Temp/Class/Test.php';
    if (file_exists($fname)) {
      $file = Asar_File::open($fname);
    } else {
      $file = Asar_File::create($fname);
    }
    ob_start();
    // In its content, add a String for class definition
    $file->write('<?php class Temp_Class_Test {} ')->save();
    ob_end_clean();
    
    // Test loading that class
    Asar::loadClass($class_name);
    
    // See if that class exists
    $this->assertTrue(class_exists($class_name, false), 'Failed to load class definition');
    // Cleanup
    $file->delete();
    rmdir('Temp/Class');
    rmdir('Temp');
  }
  /*
  function testSimpleException() {
    $obj = new Test_Parent_Class();
    try {
      $obj->throwException();
      $this->fail();
    } catch (Exception $e) {
      $this->assertEquals('Test_Parent_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_Parent_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testChildClassException() {
    $obj = new Test_Child_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Child_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_Child_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testChildClassExceptionWithoutDefinedExceptionForIt() {
    $obj = new Test_2Child_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Parent_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_2Child_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testGrandChildClassExceptionWithoutDefinedExceptionForIt() {
    $obj = new Test_GrandChild_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Child_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_GrandChild_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testDefaultToException() {
    $obj = new Test_Class_With_No_Exception();
    $this->setExpectedException('Exception');
    $obj->throwException();
  }*/
  
  function testInstantiate() {
    $testclass = 'Non_Existent_Class';

    $this->setExpectedException('Asar_Exception');
    $obj = Asar::instantiate($testclass);
  }
  
  function testInstantiateProper() {
    $testclass = 'Test_Parent_Class';
    $obj = Asar::instantiate($testclass);
    $this->assertEquals($testclass, get_class($obj), 'Wrong class');
  }
  
  function testGettingClassPrefix() {
    $obj = new Test_Parent_Class();
    $this->assertEquals(
      'Test', Asar::getClassPrefix($obj), 
      'Unable to get class prefix'
    );
  }
  
  function testGettingClassPrefix2() {
    $this->assertEquals(
      'Test2', Asar::getClassPrefix(new Test2_Class()), 
      'Unable to get class prefix'
    );
  }
  
  function testGettingClassPrefix3() {
    $this->assertEquals(
      '', Asar::getClassPrefix(new TestClassWithNoPrefix()), 
      'Unable to get class prefix'
    );
  }
  
  function testInstantiateWithArguments() {
    $testclass = 'Test_Child_Class';
    $testvar1 = 'The quick brown fox';
    $testvar2 = 50;
    $obj = Asar::instantiate($testclass, array($testvar1, $testvar2));
    $this->assertEquals($testclass, get_class($obj), 'Wrong class');
    $this->assertEquals(
      $testvar1, $obj->getAttr1(),
      'First argument was not passed properly'
    );
    $this->assertEquals(
      $testvar2, $obj->getAttr2(),
      'Second argument was not passed properly'
    );
  }
  
  function testUnInstantiableClass() {
    $testclass = 'Uninstantiable_Class';
    try {
      $obj = Asar::instantiate($testclass);
    } catch (Exception $e) {
      $this->assertEquals(
        'Asar_Exception', get_class($e),
        'Asar did not raise the right exception for instantiating an ' .
        'uninstantiable class.'
      );
      $this->assertEquals(
        "Asar::instantiate failed. Trying to instantiate the uninstantiable " .
        "class '$testclass'.",
        $e->getMessage(),
        'Asar did not set the correct exception message for instantiating an ' .
        'uninstantiable class.'
      );
    }
  }
  
  function testConstructPath() {
    $_ = DIRECTORY_SEPARATOR;
    $this->assertEquals(
      'some'. $_ . 'path' . $_ . 'to' . $_ . 'a' . $_ . 'file.php',
      Asar::constructPath('some', 'path', 'to', 'a', 'file.php'),
      'Unable to create path using "constructPath".'
    );
  }
  
  function testConstructPath2() {
    $_ = DIRECTORY_SEPARATOR;
    $this->assertEquals(
      'a'. $_ . 'path' . $_ . 'to' . $_ . 'a' . $_ . 'a',
      Asar::constructPath('a', 'path', 'to', 'a', 'a'),
      'Unable to create path using "constructPath".'
    );
  }
  
  function testConstructPath3() {
    $_ = DIRECTORY_SEPARATOR;
    $this->assertEquals(
      'a'. $_ . 'path' . $_ . 'to' . $_ . 'a' . $_ . 'a',
      Asar::constructPath('a/', 'path', 'to\\', 'a', 'a'),
      'Unable to create path using "constructPath".'
    );
  }
  
  function testConstructPath4() {
    $_ = DIRECTORY_SEPARATOR;
    $this->assertEquals(
      'a'. $_ . 'to' . $_ . 'c',
      Asar::constructPath('a', '', 'to', '', 'c'),
      'Unable to create path using "constructPath".'
    );
  }
  
  function testConstructRealPath() {
    $_ = DIRECTORY_SEPARATOR;
    $this->assertEquals(
      realpath(dirname(__FILE__) . $_  . '..' . $_ . '..'),
      Asar::constructRealPath(dirname(__FILE__), '..', '..'),
      'Unable to create path using "constructRealPath".'
    );
  }
  
  function testAsarFrameworkPath() {
    $framework_path = realpath(
      Asar::constructPath(dirname(__FILE__), '..', '..')
    );
    $this->assertEquals(
      $framework_path, Asar::getFrameworkPath(),
      'Unable to get framework path.'
    );
      
  }
  
  function testAsarFrameworkCorePath() {
    $framework_core_path = realpath(
      Asar::constructPath(dirname(__FILE__), '..', '..', 'core')
    );
    $this->assertEquals(
      $framework_core_path, Asar::getFrameworkCorePath(),
      'Unable to get framework core path.'
    );
  }
  
  function testGetFilePathIfFoundInIncludePaths() {
    $asar_path = realpath(
      Asar::constructPath(dirname(__FILE__), '..', '..', 'core', 'Asar.php')
    );
    $this->assertEquals(
      $asar_path, Asar::getFilePath('Asar.php')
    );
  }
  
  function testAsarHasInterperterProperty() {
    $this->assertClassHasAttribute(
      'interpreter', 'Asar', 'Asar class has no interpreter property.'
    );
  }
  
  
  function testSetsInterpreter() {
    $interpreter = $this->getMock('Asar_Interprets');
    Asar::setInterpreter($interpreter);
    $this->assertSame(
      $interpreter, $this->readAttribute('Asar', 'interpreter'),
      'Unable to set interpreter.'
    );
  }

  function testStartPassesAnInstanceOfApplicationToInterpreter() {
    // We show here that any object that implements the Asar_Requestable
    // interface can be started. Not just Asar_Application.
    $prefix = get_class($this). '_App1';
    $app = $this->getMock(
      'Asar_Requestable', array(), array(), $prefix . '_Application'
    );
    $interpreter = $this->getMock('Asar_Interprets', array('interpretFor'));
    $interpreter->expects($this->once())
      ->method('interpretFor')
      ->with($this->isInstanceOf(get_class($app)));
    Asar::setInterpreter($interpreter);
    Asar::start($prefix);
  }
  
  function testStartSetsDefaultInterpreterIfNoneWasSet() {
    $prefix = get_class($this). '_App2';
    $class_name = $prefix . '_Application';
    eval("
      class $class_name implements Asar_Requestable {
        function handleRequest(Asar_Request_Interface \$request) {
          return new Asar_Response;
        }
      }
    ");
    Asar::start($prefix);
    $this->assertTrue(
      $this->readAttribute('Asar', 'interpreter') 
        instanceof Asar_Interprets,
      'Asar::start() did not set a default interpreter.'
    );
  }
  
  function testSettingApplicationStartModes() {
    $modes = array(
      'Production'  => Asar::MODE_PRODUCTION,
      'Development' => Asar::MODE_DEVELOPMENT,
      'Debug'       => Asar::MODE_DEBUG
    );
    foreach ($modes as $mode => $const) {
      Asar::setMode($const);
      $this->assertEquals(
        $const, Asar::getMode(), "Mode was not set to $mode."
      );
    }
  }
  
  function testSettingModeToSomethingElseWillResortToProductionModeByDefault() {
    Asar::setMode('asdfasdf');
    $this->assertEquals(
      Asar::MODE_PRODUCTION, Asar::getMode(), 
      'Mode was not set to Production for unknown modes.'
    );
  }
  
  function testAddingDebugInformation() {
    Asar::debug('Title', 'Some debug message');
    $debug = Asar::getDebugMessages();
    $this->assertEquals(
      'Some debug message', $debug['Title'],
      'Unable to obtain debug mesesages.'
    );
  }
  
  function testClearingDebugMessages() {
    Asar::debug('foo', 'bar');
    Asar::clearDebugMessages();
    $this->assertEquals(0, count(Asar::getDebugMessages()) );
  }
  
  function testHtmlDebugOutput() {
    Asar::clearDebugMessages();
    Asar::setMode(Asar::MODE_DEBUG);
    Asar::debug('foo', 'bar');
    Asar::debug('list', array('apple', 'banana', 'cherry', 'durian'));
    Asar::debug('map', array('one' => 'alpha', 'two' => 'beta', 'three' => 'gamma'));
    $debug = new Asar_Utility_XML(Asar::debugOutputHtml());
    
    $this->assertEquals('table', $debug->getName());
    $this->assertEquals('asarwf_debug_info', $debug->getAttribute('id'));
    $this->assertEquals('Debugging Info', $debug->thead->tr->th->stringValue());
    $this->assertEquals('col', $debug->thead->tr->th->getAttribute('scope'));
    $this->assertEquals(3, count($debug->tbody->children()) );
    $row = $debug->tbody->tr;
    $this->assertEquals('foo', $row[0]->th->stringValue());
    $this->assertEquals('bar', $row[0]->td->stringValue());
    $this->assertEquals('list', $row[1]->th->stringValue());
    $this->assertEquals('row', $row[1]->th->getAttribute('scope'));
    $this->assertEquals('cherry', $row[1]->td->ul->li[2]->stringValue());
    $this->assertEquals('map', $row[2]->th->stringValue());
    $this->assertEquals('two', $row[2]->td->dl->dt[1]->stringValue());
    $this->assertEquals('beta', $row[2]->td->dl->dd[1]->stringValue());
  }
  
  function testInsertDebugInfoWhenInDebugMode() {
    $prefix = get_class($this). '_App3';
    $class_name = $prefix . '_Application';
    eval("
      class $class_name implements Asar_Requestable {
        function handleRequest(Asar_Request_Interface \$request) {
          \$r = new Asar_Response;
          \$r->setContent(
            '<html><head></head><body><h1>Hello World!</h1></body></html>'
          );
          return \$r;
        }
      }
    ");
    Asar::setMode(Asar::MODE_DEBUG);
    Asar::debug('foo', 'bar');
    ob_start();
    Asar::start($prefix);
    $content = ob_get_clean();
    $this->assertContains('<html>', $content);
    $html = new Asar_Utility_XML($content);
    $this->assertNotNull(
      $html->getElementById('asarwf_debug_info')
    );
    $this->assertEquals(
      $html->getElementById('asarwf_debug_info'), $html->body->table
    );
    $this->assertEquals(
      new Asar_Utility_XML(Asar::debugOutputHtml()),
      $html->body->table
    );
  }
  
  function testInsertDebugInfoData() {
    // Use previous definition
    $prefix = get_class($this). '_App3';
    Asar::setMode(Asar::MODE_DEBUG);
    // Clear debug messages first
    Asar::clearDebugMessages();
    ob_start();
    Asar::start($prefix);
    ob_end_clean();
    $debug = Asar::getDebugMessages();
    $key = key($debug);
    $this->assertEquals('Execution Time', $key);
    $this->assertRegExp('/[0-9]+.[0-9]{2}ms/', $debug[$key]);
    // move cursor
    next($debug); $key = key($debug);
    $this->assertEquals('Memory Used', $key);
    $this->assertRegExp('/[0-9]+.[0-9]{2}(M|K)B/', $debug[$key]);
  }
  
  
  /*
  function testSettingApplicationStartModeTest() {
    Asar::setMode(Asar::MODE_TEST);
    $this->assertEquals(Asar::MODE_TEST, Asar::getMode(), 'Mode was not set to Test');
  }
  
  function testAddingAnotherDebugInformation() {
    Asar::setMode(Asar::MODE_DEVELOPMENT);
    Asar::debug('Another Title', 'Another debug message');
    $this->assertEquals(array('Another Title'=>'Another debug message'), Asar::getDebugMessages(), 'Unable to obtain debug mesesage');
  }
  
  function testClearingDebugInformation() {
    Asar::setMode(Asar::MODE_DEVELOPMENT);
    Asar::debug('Yet Another Title', 'Yet another debug message');
    Asar::clearDebugMessages();
    $this->assertEquals(array(), Asar::getDebugMessages(), 'The debug messages was not reset');
  }
  */
  /**
   * Only use debug when in Development Mode
   *
   * @return void
   **/
  
  /*
  function testDebugOnlyWhenInDevelopmentMode()
 {
    Asar::setMode(Asar::MODE_PRODUCTION);
    Asar::debug('Another Title', 'Another debug message');
    $this->assertEquals(array(), Asar::getDebugMessages(), 'The debug messages was not reset');
  }
  */
}
