<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

/**
* 
*/
class Sample_Model_EmCalculatorTest extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		$this->calc = new Sample_Model_EmCalculator;
	}
	
	function testSetBaseFontSize()
	{
		$this->calc->setBaseFontSize(10);
		$this->assertEquals(10, $this->calc->getBaseFontSize(), 'Unable to set base font size');
	}
	
	function testSetBaseFontSizeAgain()
	{
		$expected = rand(1, 200);
		$this->calc->setBaseFontSize($expected);
		$this->assertEquals($expected, $this->calc->getBaseFontSize(), 'Unable to set base font size to ' . $expected);
	}
	
	function testSpecifiyBaseLineHeightInPixels()
	{
		$expected = rand(1, 200);
		$this->calc->setBaseLineHeight($expected);
		$this->assertEquals($expected, $this->calc->getBaseLineHeight(), 'Unable to set base line height to ' . $expected);
	}
	
	function testGettingBaseLineHeightInEms()
	{
		$base_font_size = rand(10, 20);
		$base_line_height = rand(20, 30);
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = round($base_line_height / $base_font_size, 4);
		$this->assertEquals($expected, $this->calc->getBaseLineHeight('em'),
			"Unable to obtain base line height of $expected  from base: $base_font_size and base line-height: $base_line_height" );
	}
	
	function testSetInheritedSizeInPixels()
	{
		$expected = rand(1, 200);
		$this->calc->setInherited($expected);
		$this->assertEquals($expected, $this->calc->getInherited(), 'Unable to set inherited  size to ' . $expected);
	}
	
	function testSetPrecision()
	{
		$expected = rand(0, 8);
		$this->calc->setPrecision($expected);
		$this->assertEquals($expected, $this->calc->getPrecision(), 'Unable to set precision to ' . $expected);
	}
	
	function testSettingPrecisionThenGettingBaseLineHeightInEms()
	{
		$base_font_size = 11;
		$base_line_height = 16;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$this->calc->setPrecision(5);
		$expected = 1.45455;
		$this->assertEquals($expected, $this->calc->getBaseLineHeight('em'),
			"Unable to obtain base line height of $expected  from base: $base_font_size and base line-height: $base_line_height" );
	}
	
	function testSettingPrecisionThenGettingBaseLineHeightInEmsAgain()
	{
		$base_font_size = 11;
		$base_line_height = 18;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$this->calc->setPrecision(3);
		$expected = 1.636;
		$this->assertEquals($expected, $this->calc->getBaseLineHeight('em'),
			"Unable to obtain base line height of $expected  from base: $base_font_size and base line-height: $base_line_height" );
	}
	
	function testNotSettingPrecisionShouldDefaultTo4()
	{
		$this->assertEquals(4, $this->calc->getPrecision(), 'Unable to set precision to ' . $expected);
	}
	
	function testGettingCalculatedEmValueFromPixels()
	{
		$base_font_size = 10;
		$this->calc->setBaseFontSize($base_font_size);
		$this->assertEquals(1.5, $this->calc->getInEms(15), 'The calculated em value is not correct');
	}
	
	function testGettingCalculatedEmValueFromPixelsAgain()
	{
		$base_font_size = 12;
		$this->calc->setBaseFontSize($base_font_size);
		$this->assertEquals(1.25, $this->calc->getInEms(15), 'The calculated em value is not correct');
	}
	
	function testGettingCalculatedEmValueFromPixelsOnceMore()
	{
		$base_font_size = 11;
		$this->calc->setBaseFontSize($base_font_size);
		$this->assertEquals(1.3636, $this->calc->getInEms(15), 'The calculated em value is not correct');
	}
	
	function testGettingCalculatedEmValueFromPixelsOnceMoreWithDefinedPrecision()
	{
		$base_font_size = 11;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setPrecision(7);
		$this->assertEquals(1.3636364, $this->calc->getInEms(15), 'The calculated em value is not correct');
	}
	
	function testGettingCalculatedLineHeight()
	{
		$base_font_size = 10;
		$base_line_height = 20;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = 1.3333;
		$this->assertEquals($expected, $this->calc->getLineHeight(15), 'The calculated line-height em value is not correct');
	}
	
	function testGettingCalculatedLineHeightAgain()
	{
		$base_font_size = 11;
		$base_line_height = 20;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = 1.6667;
		$this->assertEquals($expected, $this->calc->getLineHeight(12), 'The calculated line-height em value is not correct');
	}
	
	function testGettingCalculatedLineHeightOnceMore()
	{
		$base_font_size = 9;
		$base_line_height = 10;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = 1.8182;
		$this->assertEquals($expected, $this->calc->getLineHeight(11), 'The calculated line-height em value is not correct');
	}
	
	function testGettingCalculatedLineHeightWithPrecision()
	{
		$base_font_size = 11;
		$base_line_height = 16;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$this->calc->setPrecision(7);
		$expected = 1.3333333;
		$this->assertEquals($expected, $this->calc->getLineHeight(12), 'The calculated line-height em value is not correct');
	}
	
	function testGettingCalculatedLineHeightWhereTheOutputValueIsMoreThanBaseLineHeight()
	{
		$base_font_size = 11;
		$base_line_height = 20;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = 1.7391;
		$this->assertEquals($expected, $this->calc->getLineHeight(23), 'The calculated line-height em value is not correct');
	}
	
	function testGettingCalculatedLineHeightWhereTheOutputValueIsMoreThanBaseLineHeightAgain()
	{
		$base_font_size = 11;
		$base_line_height = 16;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = 1.3333;
		$this->assertEquals($expected, $this->calc->getLineHeight(24), 'The calculated line-height em value is not correct');
	}
	
	function testGettingCalculatedLineHeightWhereTheOutputValueIsMoreThanBaseLineHeightOnceMore()
	{
		$base_font_size = 11;
		$base_line_height = 16;
		$this->calc->setBaseFontSize($base_font_size);
		$this->calc->setBaseLineHeight($base_line_height);
		$expected = 1.1707;
		$this->assertEquals($expected, $this->calc->getLineHeight(41), 'The calculated line-height em value is not correct');
	}
}
