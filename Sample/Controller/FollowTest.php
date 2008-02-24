<?php

class Sample_Controller_FollowTest extends Asar_Controller {
	
	function GET() {
		$this->view['what_to_say'] = 'I\'ve been followed!';
	}
}
?>