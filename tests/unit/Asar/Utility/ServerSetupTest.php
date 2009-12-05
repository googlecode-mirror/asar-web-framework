<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Utility_ServerSetupTest extends Asar_Test_Helper {
    
    public function setUp()
    {
        $this->S = new Asar_Utility_ServerSetup('asar-test.local');
    }
    
    public function testCreatingNewServerSetup()
    {
        $server = new Asar_Utility_ServerSetup('a-domain.local');
        $this->assertSame(
            $server, Asar_Utility_ServerSetup::getServer('a-domain.local'),
            'Unable to create server setup.'
        );
    }
    
    public function testGetServerName()
    {
        $this->assertEquals(
            'asar-test.local', $this->S->getName(),
            'Unable to get server name'
        );
    }
    
    public function testSetWebRoot()
    {
        $this->S->setWebRoot('/a/path/to/somewhere');
        $this->assertEquals(
            '/a/path/to/somewhere', $this->S->getWebRoot(),
            'Unable to set web root.'
        );
    }
    
    public function testSetUpCreateFrontController()
    {
        $webroot = self::createDir('webroot');
        $this->S->setWebRoot($webroot);
        $this->S->setUp('AppName');
        $FC = $webroot . DIRECTORY_SEPARATOR . 'index.php';
        $this->assertTrue(
            file_exists($FC),
            'Unable to create front controller file.'
        );
        $contents = file_get_contents($FC);
        $this->assertEquals(
            0, strpos($contents, "<?php\n"),
            'Unable to find <?php preprocessing directive at the beginning.'
        );
        $this->assertContains(
            "require_once 'Asar.php';", $contents,
            'The contents of the controller file are not correct.'
        );
        $this->assertContains(
            "Asar::start('AppName');", $contents,
            'The Asar::start() declaration was not found.'
        );
    }
    
    public function testSetUpCreatesHtAccessFile()
    {
        $htaccess_contents = "<IfModule mod_rewrite.c>\n" .
            "RewriteEngine On\n".
            "RewriteBase /\n".
            "RewriteCond %{REQUEST_FILENAME} !-f\n".
            "RewriteCond %{REQUEST_FILENAME} !-d\n".
            "RewriteRule . /index.php [L]\n".
            "</IfModule>";
        $webroot = self::createDir('webroot');
        $this->S->setWebRoot($webroot);
        $this->S->setUp('AppName');
        $H = $webroot . DIRECTORY_SEPARATOR . '.htaccess';
        $this->assertTrue(
            file_exists($H),
            'Unable to create .htaccess file.'
        );
        $contents = file_get_contents($H);
        $this->assertContains(
            $htaccess_contents, $contents,
            'Unable to find expected .htaccess contents.'
        );
    }
}
