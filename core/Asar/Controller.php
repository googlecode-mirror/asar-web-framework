<?php
/**
 * Asar_Controller class definition - Asar Web Framework Core
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to wayne@asartalo.org so we can send you a copy immediately.
 * 
 * @package   Asar-Core
 * @copyright Copyright (c) 2007-2008, Wayne Duran <wayne@asartalo.org>.
 * @since     0.1
 * @version   $Id$
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.google.com/p/asar-web-framework
 */

/**
 * Asar_Controller
 *
 * Asar_Controller is the basic request handler. In REST terms
 * you can think of Asar_Controller as the resource, although
 * this is not accurate.
 *
 * @package Asar-Core
 * @todo Write a better description
 * @todo How do we handle PUT request methods?
 * @todo How do we handle files uploaded?
 **/
abstract class Asar_Controller extends Asar_Base implements Asar_Requestable {
    private $response = null;
	
	function handleRequest(Asar_Request $request, array $arguments = null) {
		$this->response = new Asar_Response;
		$method = $request->getMethod();
		$this->response->setContent($this->$method());
	    
		return $this->response;
	}
	
	public function GET() {
	    $this->response->setStatus(405);
	}
	
	public function POST() {
	    $this->response->setStatus(405);
	}
	
	public function PUT() {
	    $this->response->setStatus(405);
	}
	
	public function DELETE() {
	    $this->response->setStatus(405);
	}
	
	public function HEAD() {
	    $this->GET();
	}
	
	
}