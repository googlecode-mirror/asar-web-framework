<?php

/**
 * @todo The logic of this class needs a lot of rethinking and rethinking. It smells ugly.
 */
class Asar_Controller_Default extends Asar_Base implements Asar_Requestable {
	
	function handleRequest(Asar_Request $request, array $arguments = NULL)
	{
		$this->response = $request->getContent(); 
		$this->view = new Asar_Template_Html;
		$this->view->setTemplate('Asar/View/Default/ALL.html.php');
		$this->view->setLayout('Asar/View/Layout.html.php');
		switch ($this->response->getStatus()) {
			case 404:
				$this->view['message'] = 'Sorry, we were unable to find the resource you were looking for. Please check that you got the address or URL correctly. If that is the case, please email the administrator. Thank you and please forgive the inconvenience.';
				$this->view['heading'] = 'File Not Found (404)';
				break;
			case 405:
				//$method = ;
				$this->view['message'] = "The HTTP Method '{$request->getMethod()}' is not allowed for this resource.";
				$this->view['heading'] = "Method Not Allowed (405)";
				break;
			default:
				$this->view['message'] = 'The application has encountered some problems. Please email the administrator.';
				$this->view['heading'] = 'Internal Server Error (500)';
				break;
		}
		
		$this->response->setContent($this->view->fetch());
		return $this->response;
	}
}