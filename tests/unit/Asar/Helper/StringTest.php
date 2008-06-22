<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Helper_StringTest extends PHPUnit_Framework_TestCase {
	
	function testCamelCasingAnUnderscoredString()
	{
		$str = 'i_think_i_am_inside';
		$expected = 'IThinkIAmInside';
		$this->assertEquals($expected, Asar_Helper_String::camelCase($str),
			"'$str' was not properly camel-cased to $expected"
		);
	}
	
	function testCamelCasingADasheddString()
	{
		$str = 'shut-up-and-let-me-go';
		$expected = 'ShutUpAndLetMeGo';
		$this->assertEquals($expected, Asar_Helper_String::camelCase($str),
			"'$str' was not properly camel-cased to $expected"
		);
	}
	
	function testLowerCamelCasingAnUnderscoredString() {
		$str = 'this_hurts_i_told_you_so';
		$expected = 'thisHurtsIToldYouSo';
		$this->assertEquals($expected, Asar_Helper_String::lowerCamelCase($str),
			"'$str' was not properly lower camel-cased to $expected"
		);
	}
	
	function testLowerCamelCasingADashedString() {
		$str = 'nothing-but-the-girls-ah-ah';
		$expected = 'nothingButTheGirlsAhAh';
		$this->assertEquals($expected, Asar_Helper_String::lowerCamelCase($str),
			"'$str' was not properly lower camel-cased to $expected"
		);
	}
	
	function testUnderscoringACamelCasedString() {
		$str = 'FourLittleWords';
		$expected = 'four_little_words';
		$this->assertEquals($expected, Asar_Helper_String::underscore($str),
			"'$str' was not properly underscore to $expected"
		);
	}
	
	function testDashingACamelCasedString() {
		$str = 'YourJeansWereOnceSoClean';
		$expected = 'your-jeans-were-once-so-clean';
		$this->assertEquals($expected, Asar_Helper_String::dash($str),
			"'$str' was not properly dash to $expected"
		);
	}
}