<?php
/**
 * Created on Jun 21, 2007
 * 
 * @author     Wayne Duran
 */

require_once('Asar/Utility/RandomStringGenerator.php');

class Asar_Utility_RandomStringGeneratorTest extends PHPUnit_Framework_TestCase {
	
	protected $RSG;
	protected $RandomNumber;
	protected $CharacterLimit;
	protected $StrLength;
	protected $CharacterList = array();
	
	protected function setUP() {
		
		try {
			$this->RSG = Asar_Utility_RandomStringGenerator::instance();
		} catch (Exception $e) {
		}
		
		$this->CharacterList = str_split('_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
		$this->CharacerLimit = count($this->CharacterList) -1;
		$this->RandomNumber = mt_rand(0, $this->CharacterLimit);
		$this->StrLength = mt_rand(1, 40);
	}
	
	public function testSingleton() {
		
		try {
			$this->RSG = Asar_Utility_RandomStringGenerator::instance();
			$b = Asar_Utility_RandomStringGenerator::instance();
			throw new Exception($this->RandomNumber);
		} catch (Exception $e) {
			$this->assertEquals(get_class($this->RSG), 'Asar_Utility_RandomStringGenerator', 'Not an instance Asar_Utility_RandomStringGenerator');
		}
		
		$this->assertSame($this->RSG, $b, 'Not Singleton');
	}
	
	public function testGetValue() {
		$this->assertEquals(
							$this->RSG->getValue($this->RandomNumber),
							$this->CharacterList[$this->RandomNumber],
							'Character did not match expected value');
		
		$this->assertTrue(preg_match('/[A-Za-z_0-9]+/', $this->RSG->getValue($this->RandomNumber)) > 0, 'String may be out of range');
	}
	
	public function testGetRandomAlphaNumeric() {
		$test = $this->RSG->getAlphaNumeric($this->StrLength);
		
		$this->assertEquals(strlen($test), $this->StrLength, 'Returned string of different length');
		$this->assertTrue(strlen($test) > 0, 'Empty String');
		$this->assertTrue(preg_match('/[A-Za-z0-9]+/', $test) > 0, 'Not Alpha-numeric characters');
	}
	
	public function testGetRandomAlpha() {
		$test = $this->RSG->getAlpha($this->StrLength);
		
		$this->assertEquals(strlen($test), $this->StrLength, 'Returned string of different length');
		$this->assertTrue(preg_match('/[A-Za-z]+/', $test) > 0, 'Not Alpha-numeric characters');
	}
	
	
	public function testGetRandomNumeric() {
		$test = $this->RSG->getNumeric($this->StrLength);
		
		$this->assertEquals(strlen($test), $this->StrLength, 'Returned string of different length');
		$this->assertTrue(preg_match('/[0-9]+/', $test) > 0, 'Not Numeric characters');
	}
	
	
	public function testGetRandomUppercaseAlpha() {
		$test = $this->RSG->getUppercaseAlpha($this->StrLength);
		
		$this->assertEquals(strlen($test), $this->StrLength, 'Returned string of different length');
		$this->assertTrue(preg_match('/[A-Z]+/', $test) > 0, 'Not uppercase alpha characters');
	}
	
	
	public function testGetRandomLowercaseAlpha() {
		$test = $this->RSG->getLowercaseAlpha($this->StrLength);
		
		$this->assertEquals(strlen($test), $this->StrLength, 'Returned string of different length');
		$this->assertTrue(preg_match('/[a-z]+/', $test) > 0, 'Not lowercase alpha characters');
	}
	
	
	public function testGetRandomPhpLabel() {
		$test = $this->RSG->getPhpLabel($this->StrLength);
		
		$this->assertEquals(strlen($test), $this->StrLength, 'Returned string of different length');
		$this->assertTrue(preg_match('/[a-zA-Z_][a-zA-Z0-9_]*/', $test) > 0, 'Not valid PHP label characters');
	}
	
	
}
