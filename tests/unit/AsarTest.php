<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';
require_once 'Asar/Test/Helper.php';

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
class AsarTest_Controller_Index extends Asar_Controller{
	function GET() {
		return 'Hello World';
	}
}

class Test2_Class {}
class TestClassWithNoPrefix {}

abstract class Uninstantiable_Class {}





class AsarTest extends Asar_Test_Helper {
	
	protected function setUp() {
		$_SERVER['SERVER_NAME'] = 'sample.website';
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
			$this->fail('Must not reach this point');
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
	
	function testGettingClassPrefix() {
		$obj = new Test_Parent_Class();
		$this->assertEquals('Test', Asar::getClassPrefix($obj), 'Unable to get class prefix');
	}
	
	function testGettingClassPrefix2() {
		$this->assertEquals('Test2', Asar::getClassPrefix(new Test2_Class()), 'Unable to get class prefix');
	}
	
	function testGettingClassPrefix3() {
		$this->assertEquals('', Asar::getClassPrefix(new TestClassWithNoPrefix()), 'Unable to get class prefix');
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
	
	function testStartTestApplicationOnClientObjectSetsAResponseObjectOnClient() {
		$testapp = 'AsarTest';
		$client = new Asar_Client;
		$client->createRequest('/', array('method'=>'GET'));
		Asar::start('AsarTest', $client);
		$this->assertTrue($client->getResponse() instanceof Asar_Response, 'start method did not set response for client');
	}
	
	function testStartTestApplicationOnClientObjectInvokesApplicationAndSetsResponseObjectOnClient() {
		$testapp = 'AsarTest';
		$client = new Asar_Client;
		$client->createRequest('/', array('method'=>'GET'));
		Asar::start('AsarTest', $client);
		$this->assertEquals($client->getResponse()->getContent(), 'Hello World','start method did not set response for client');
	}
	
	function testStartingWithNoDefinedApplicationInvokesTheDefaultClient() {
		$this->setExpectedException('Asar_Client_Default_Exception');
		$testapp = 'AsarTest';
		Asar::start('AsarTest');
	}
	
	function testSettingApplicationStartModeProduction() {
		Asar::setMode(Asar::PRODUCTION_MODE);
		$this->assertEquals(Asar::PRODUCTION_MODE, Asar::getMode(), 'Mode was not set to Production');
	}
	
	function testSettingApplicationStartModeDevelopment() {
		Asar::setMode(Asar::DEVELOPMENT_MODE);
		$this->assertEquals(Asar::DEVELOPMENT_MODE, Asar::getMode(), 'Mode was not set to Development');
	}
	
	function testSettingApplicationStartModeTest() {
		Asar::setMode(Asar::TEST_MODE);
		$this->assertEquals(Asar::TEST_MODE, Asar::getMode(), 'Mode was not set to Test');
	}
	
	function testSettingApplicationStartModeToSomethingElseWillResortToProductionModeByDefault() {
		Asar::setMode('asdfasdf');
		$this->assertEquals(Asar::PRODUCTION_MODE, Asar::getMode(), 'Mode was not set to Production');
	}
	
	function testAddingDebugInformation() {
		Asar::debug('Title', 'Some debug message');
		$this->assertEquals(array('Title'=>'Some debug message'), Asar::getDebugMessages(), 'Unable to obtain debug mesesage');
	}
	
	function testAddingAnotherDebugInformation() {
		Asar::debug('Another Title', 'Another debug message');
		$this->assertEquals(array('Another Title'=>'Another debug message'), Asar::getDebugMessages(), 'Unable to obtain debug mesesage');
	}
	
	function testClearingDebugInformation() {
		Asar::debug('Yet Another Title', 'Yet another debug message');
		Asar::clearDebugMessages();
		$this->assertEquals(NULL, Asar::getDebugMessages(), 'The debug messages was not reset');
	}
}
