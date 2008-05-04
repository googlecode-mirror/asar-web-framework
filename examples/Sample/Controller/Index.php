<?php

class Sample_Controller_Index extends Asar_Controller {
	
	protected $map = array(
		'follow' => 'FollowTest',
		'em_calculator' => 'EmCalculator'
		);
	
	function GET() {
		$this->view['hello'] = 'Hello World!';
		$this->view['follow_url'] = '/follow/';
		$this->view['em_calculator'] = '/em_calculator/';
	}
}
