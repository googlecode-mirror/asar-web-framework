<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Base_Child_Temp extends Asar_Base {
    function throwException() {
        $this->exception('Exception thrown from Asar_Base_Child_Temp');
    }
    
    function amIDebugging() {
        return $this->isDebugMode();
    }
}

class Asar_BaseTest extends PHPUnit_Framework_TestCase {

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    protected function setUp()
    {
        Asar::setMode(Asar::MODE_PRODUCTION);
        $this->ABC = new Asar_Base_Child_Temp();
    }
    
    protected function tearDown()
    {
        Asar::setMode(Asar::MODE_PRODUCTION);
    }

    public function testThrowingException()
    {
        try {
            $this->ABC->throwException();
            $this->assertTrue(false, 'Exception not thrown');
        } catch (Exception $e) {
            $this->assertEquals('Asar_Base_Exception', get_class($e), 'Wrong exception thrown');
            $this->assertEquals('Exception thrown from Asar_Base_Child_Temp', $e->getMessage(), 'Exception message mismatch');
        }
    }
    
    /**
     * Should enable calling Asar::debug directly from class
     *
     * @return void
     **/
    public function testDebugging()
    {
        Asar::setMode(Asar::MODE_DEVELOPMENT); // Debugging only works in development mode
        $debug_message = 'My custom debug message.';
        $debug_key = 'the_key';
        $this->ABC->debug($debug_key, $debug_message);
        $debug = Asar::getDebugMessages();
        $this->assertTrue(array_key_exists($debug_key, $debug), 'The debug title was not found');
        $this->assertEquals($debug_message, $debug[$debug_key], 'The debug message was not found');
    }
    
    /**
     * See if we're in debugging mode
     *
     * @return void
     **/
    public function testSeeIfDebuggingIsEnabled()
    {
        Asar::setMode(Asar::MODE_DEVELOPMENT);
        $this->assertTrue($this->ABC->amIDebugging(), 'Debug mode was not properly set');
        
    }
    
    
}