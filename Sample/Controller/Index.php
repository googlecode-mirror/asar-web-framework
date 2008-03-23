<?php

class Sample_Controller_Index extends Asar_Controller {
	
	protected $map = array(
		'follow' => 'FollowTest'
		);
	
	function GET() {
		$this->view['hello'] = 'Hello World!';
		$this->view['follow_url'] = '/follow/';
	}
}
