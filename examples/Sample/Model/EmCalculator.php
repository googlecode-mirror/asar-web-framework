<?php

/**
* EmCalculator Class calculates pixel values to ems
*/
class Sample_Model_EmCalculator
{
	private $base;
	private $base_line_height;
	private $inherited;
	private $precision = 4;
	
	function setBaseFontSize($value)
	{
		$this->base = $value;
	}
	
	function getBaseFontSize()
	{
		return $this->base;
	}
	
	function setBaseLineHeight($value)
	{
		$this->base_line_height = $value;
	}
	
	function getBaseLineHeight($unit = null)
	{
		if ($unit == 'em') {
			$in_ems = $this->base_line_height / $this->base;
			return round($in_ems, $this->precision);
		} else {
			return $this->base_line_height;
		}
	}
	
	function setInherited($value)
	{
		$this->inherited = $value;
	}
	
	function getInherited()
	{
		return $this->inherited;
	}
	
	function setPrecision($value)
	{
		$this->precision = $value;
	}
	
	function getPrecision()
	{
		return $this->precision;
	}
	
	function getInEms($expected_font_size)
	{
		return round($expected_font_size / $this->base, $this->precision);
	}
	
	function getLineHeight($expected_font_size)
	{
		if ($expected_font_size > $this->base_line_height) {
			$line_height_in_pixels = ceil($expected_font_size / $this->base_line_height) * $this->base_line_height;
		} else {
			$line_height_in_pixels = $this->base_line_height;
		}
		$line_height = round($line_height_in_pixels / $expected_font_size, $this->precision);
		return $line_height;
	}
}
