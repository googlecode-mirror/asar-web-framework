<?php
require_once realpath(dirname(__FILE__) . '/../../../config.php');

class Asar_Test_ServerTest extends Asar_Test_Helper {
    
    public function setUp()
    {
        $this->test_server_path = Asar::constructRealPath(
            Asar::getFrameworkPath(), 'tests', 'data'
        ) . DIRECTORY_SEPARATOR . 'test-server';
        if (file_exists($this->test_server_path)) {
            unlink($this->test_server_path);
        }
    }
    
    public function tearDown()
    {
        if (file_exists($this->test_server_path)) {
            unlink($this->test_server_path);
        }
    }

    public function testSetupFixtures()
    {
        Asar_Test_Server::setUp(array('fixture' => 'normal'));
        
        $this->assertEquals(
            Asar::constructRealPath(
                Asar::getFrameworkPath(), 'tests', 'data', 
                'test-server-fixtures', 'normal'
            ),
            realpath($this->test_server_path),
            'The test-server directory does not ' .
                'point to the normal fixture directory.'            
        );
    }
    
    public function testSetupWithFullPath()
    {
        $rand = Asar_Utility_RandomStringGenerator::instance();
        $deep = mt_rand(1, 5);
        $subdirs = '';
        for ($i = 0; $i < $deep; $i++) {
            $subdirs .= $rand->getAlphaNumeric(mt_rand(3, 10));
            if ($i !== $deep)
                $subdirs .= DIRECTORY_SEPARATOR;
        }
        
        $serverdir = self::createDir($subdirs);
        Asar_Test_Server::setUp(array('path' => $serverdir));
        $this->assertEquals(
            realpath($serverdir), realpath($this->test_server_path),
            'The test-server directory does not ' .
                'point to the specified directory.'
        );
    }
}
