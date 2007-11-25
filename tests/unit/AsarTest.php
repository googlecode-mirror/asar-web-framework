<?php

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

class Test_Application extends Asar_Application {}
class Test_Router extends Asar_Router{}
if (!class_exists('Test_Client', false)) {
	class Test_Client extends Asar_Client {}
}

abstract class Uninstantiable_Class {}
 
class AsarTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		
	}
	
	protected function tearDown() {
		Asar::reset();
	}
	
	function testGetVersion() {
		$this->assertEquals(Asar::getVersion(), '0.0.1pa', 'Unable to get proper version');
	}
	
	function testSetAsarPath() {
		$testpath = '/testpathxyz';
		Asar::setAsarPath($testpath);
		
		$this->assertTrue( strpos(get_include_path(), $testpath) !== FALSE, 'Path setting not found');
	}
	
	// @todo: Create test for the part of the code that includes the file
	function testLoadClass() {
		try {
			Asar::loadClass('Test_Dummy_Class');
			$this->assertTrue(false, 'Must not reach this point');
		} catch (Exception $e) {
			$this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
			$this->assertEquals(0, strpos($e->getMessage(), 'Class definition file for the class Test_Dummy_Class does not exist.'), 'Did not attempt to load the class definition');
		}
	}
	
	function testLoadingExistingClass() {
		$class_name = 'Temp_Class_Test';    
		
		// Test first if class exists. It must not.
		$this->assertFalse(class_exists($class_name, false), 'Class definition was loaded already!');
		
		// Create a file that follows naming convention
		mkdir('Temp/Class', 0777, true);
		$file = Asar_File::create('Temp/Class/Test.php');
		
		// In its content, add a String for class definition
		$file->write('
			<?php
			class Temp_Class_Test {}
			?>
		')->save();
		
		// Test loading that class
		Asar::loadClass($class_name);
		
		// See if that class exists already
		
		$this->assertTrue(class_exists($class_name, false), 'Failed to load class definition');
		// Cleanup
		$file->delete();
		rmdir('Temp/Class');
		rmdir('Temp');
	}
	
	function testStartWithNonExistentApp() {
		$dummyapp = 'DummyApp';
		try {
			Asar::start('DummyApp');
		} catch (Exception $e) {
			// must attempt to load application class
			$this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
			$this->assertEquals(0, strpos($e->getMessage(), 'Class definition file for the class DummyApp_Application does not exist.'), 'Did not attempt to load the class definition');
			
		}
	}
	
	function testStartWithTestApplication() {
		$testapp = 'Test';
		try {
			Asar::start('Test');
			// Test if we get the right client
			// Client must default to 'Asar_Client'
			$client = Asar::getLastClientLoaded();
			$app    = Asar::getAppWithClient($client->getName());
		} catch (Exception $e) {
			$this->assertTrue(false, 'Exception thrown: '. get_class($e) . ' , '. $e->getMessage());
		}
		$this->assertEquals('Asar_Client', get_class($client), 'Wrong Client loaded');
		$this->assertEquals('Test_Application', get_class($app), 'Wrong application loaded');
	}
	
	function testStartWithCustomClient() {
		$testapp = 'Test';
		$client_name = 'What a wonderful world';
		$client = new Test_Client();
		$client->setName($client_name);
		try {
			Asar::start('Test', $client);
		} catch (Exception $e) {
			$this->assertTrue(false, 'Exception thrown: '. get_class($e) . ' , '. $e->getMessage());
		}
		$testClient = Asar::getClient($client->getName());
		$app = Asar::getAppWithClient($client->getName());
		$this->assertSame($client, $testClient, 'Client passed was not found');
		$this->assertEquals('Test_Client', get_class($testClient), 'Wrong Client loaded');
		$this->assertEquals('Test_Application', get_class($app), 'Wrong application loaded');
	}
	
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
	}
	
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
	
	function testInstantiateWithArguments() {
		$testclass = 'Test_Child_Class';
		$testvar1 = 'The quick brown fox';
		$testvar2 = 50;
		$obj = Asar::instantiate($testclass, array($testvar1, $testvar2));
		$this->assertEquals($testclass, get_class($obj), 'Wrong class');
		$this->assertEquals($testvar1, $obj->getAttr1(), 'First argument was not passed properly');
		$this->assertEquals($testvar2, $obj->getAttr2(), 'Second argument was not passed properly');
	}
	
	function testUnInstantiableClass() {
		$testclass = 'Uninstantiable_Class';
		$this->setExpectedException('Asar_Exception');
		$obj = Asar::instantiate($testclass);
	}
}

?>