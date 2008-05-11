<?php

/**
* 
*/
class Sample_Controller_EmCalculator extends Asar_Controller
{
	function GET()
	{
		
	}
	
	function POST()
	{
		$this->calc = new Sample_Model_EmCalculator;
		$post = $this->request->getContent();
		$this->calc->setBaseFontSize($post['emcalc_base-font-size']);
		$this->calc->setBaseLineHeight($post['emcalc_base-line-height']);
		$this->view['font-size'] = $this->calc->getInEms($post['emcalc_expected-font-size']);
		$this->view['line-height'] = $this->calc->getLineHeight($post['emcalc_expected-font-size']);
	}
}
