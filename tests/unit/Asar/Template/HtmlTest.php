<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'Asar.php';

class Asar_Template_HtmlTest extends Asar_Test_Helper {
    public static $layout_name = 'temp/main.php';
	public static $layout_path;
	public static $layout_contents = '
<html>
<head>
<title><?= $this[\'title\'] ?> This is the Title</title>
</head>
<body>
<h1>Main Template Here</h1>
<?=$contents ?>
</body>
</html>';
	private static $template_name = 'inc.php';
	private static $template_path;
	private static $template_contents = '
<h2>This is in included template</h2>
<p><?= $var ?></p>
<p><strong><?= $this[\'var2\'] ?></strong></p>';
	
    protected function setUp() {
        $this->T = new Asar_Template_Html;
	    self::newFile(self::$template_name,self::$template_contents);
	    self::$template_path = self::getPath(self::$template_name);
	    self::newFile(self::$layout_name,self::$layout_contents);
	    self::$layout_path = self::getPath(self::$layout_name);
		$this->T['var'] = 'This is a test';
        $this->T['var2'] = 'Another test';
    }
    
    public function testLayout()
    {
        $this->T->setLayout(self::$layout_path);
        $haystack = $this->T->fetch(self::$template_path);
        $this->assertContains('<p>This is a test</p>', $haystack, 'Template variable was not set');
        $this->assertContains('<html>', $haystack, 'Did not include layout template');
    }
    
    public function testGetLayoutPath()
    {
        $test_path = 'test/path/to/layout';
        $this->T->setLayout($test_path);
        $this->assertEquals($test_path, $this->T->getLayout(), 'Layout path was not set');
    }

	public function testSettingLayoutVariable()
	{
		$this->T->setLayout(self::$layout_path);
		$this->T->setLayoutVar('title', 'TestTitle');
		$this->assertContains('TestTitle', $this->T->fetch(self::$template_path), 'The layout property of the template is not a template object');
	}
}
