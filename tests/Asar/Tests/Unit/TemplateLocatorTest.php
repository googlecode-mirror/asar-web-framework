<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\TemplateLocator;
use \Asar\Request;

class TemplateLocatorTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->tempdir = $this->getTempDir();
    $this->TFM = $this->getTFM();
    $this->clearTestTempDirectory();
    $this->content_negotiator = $this->getMock('Asar\ContentNegotiator');
    $engine_extensions = array('php', 'haml');
    $this->RT = new TemplateLocator(
      $this->content_negotiator,
      $this->tempdir, $engine_extensions
    );
  }
  
  function tearDown() {
    $this->clearTestTempDirectory();
  }
  
  /**
   * @dataProvider dataArgsPasssingToNegotiator
   */
  function testArgsPasssingToNegotiator(
    $resource_name, $request_options,
    $files, $available_types
  ) {
    foreach ($files as $file) {
      $this->TFM->newFile($file, '');
    }
    $request = new Request($request_options);
    $this->content_negotiator->expects($this->once())
      ->method('negotiateFormat')
      ->with($request->getHeader('Accept'), $available_types);
    $this->RT->locateFor($resource_name, $request);
  }
  
  function dataArgsPasssingToNegotiator() {
    return array(
      
      array(
        'Churva_Resource_Index', array(),
        array('Representation/Index.GET.html.php'),
        array('text/html')
      ),
      
      array(
        'Churva_Resource_Index', array(),
        array(
          'Representation/Index.GET.html.php','Representation/Index.GET.xml.php'
        ),
        array('text/html', 'application/xml')
      ),
      
      array(
        'Churva_Resource_Index', array(),
        array(
          'Representation/Index.GET.xhtml.php',
          'Representation/Index.GET.html.php'
        ),
        array('application/xhtml+xml', 'text/html')
      ),
      
      array(
        'Churva_Resource_Index', array(),
        array(
          'Representation/Index/GET.xhtml.php',
          'Representation/Index/GET.html.php'
        ),
        array('application/xhtml+xml', 'text/html')
      ),
      
      array(
        'Churva_Resource_Index', array(),
        array(
          'Representation/Index.GET.xhtml.php',
          'Representation/Foo.GET.html.php',
          'Representation/Index.POST.xml.php',
        ),
        array('application/xhtml+xml')
      ),
      
      array(
        'Churva_Resource_Foo_Bar_Boo', array(),
        array(
          'Representation/Foo/Bar/Boo.GET.xhtml.php',
          'Representation/Foo/Bar/Boo.GET.html.php',
          'Representation/Foo/Bar/Boo.GET.xml.php',
        ),
        array('application/xhtml+xml', 'text/html', 'application/xml')
      ),
    );
  }
  
  /**
   * @dataProvider dataTemplateLocatorBasicInvocationSuccess
   */
  function testTemplateLocatorBasicInvocationSuccess(
    $resource_name, $request_options, $negotiator_returns,
    $expected_file, $files
  ) {
    foreach ($files as $file) {
      $this->TFM->newFile($file, '');
    }
    $this->content_negotiator->expects($this->once())
      ->method('negotiateFormat')
      ->will($this->returnValue($negotiator_returns));
    $request = new Request($request_options);
    $this->assertEquals(
      $this->TFM->getPath($expected_file),
      $this->RT->locateFor($resource_name, $request)
    );
  }
  
  function dataTemplateLocatorBasicInvocationSuccess() {
    return array(
      array(
        'Churva_Resource_Index', array(), 'text/html',
        'Representation/Index.GET.html.php',
        array('Representation/Index.GET.html.php')
      ),
      array(
        'Churva_Resource_Index', array('method' => 'POST'), 'text/html',
        'Representation/Index.POST.html.php',
        array('Representation/Index.POST.html.php')
      ),
      array(
        'Churva_Resource_Index',
        array('headers' => array('Accept' => 'application/xml')),
        'application/xml',
        'Representation/Index.GET.xml.php',
        array('Representation/Index.GET.xml.php')
      ),
      array(
        'Churva_Resource_Index', array(), 'text/html',
        'Representation/Index.GET.html.haml',
        array('Representation/Index.GET.html.haml')
      ),
      array(
        'Moonda_Resource_AResource_Go', array(), 'text/html',
        'Representation/AResource/Go.GET.html.php',
        array('Representation/AResource/Go.GET.html.php')
      ),
    );
  }
  
  function testTemplateLocatorReturnsFalseWhenNoTemplateIsFoundForResource() {
    $this->assertSame(
      FALSE, $this->RT->locateFor('Some_Resource', new Request)
    );
  }
  
  function testReturnFalseWhenNegotiatorReturnsFalse() {
    $this->TFM->newFile('Representation/AResource/Go.GET.html.php', '');
    $this->content_negotiator->expects($this->once())
      ->method('negotiateFormat')
      ->will($this->returnValue(FALSE));
    $this->assertEquals(
      FALSE, $this->RT->locateFor(
        'Moonda_Resource_AResource_Go', new Request
      )
    );
  }
  
  /**
   * @dataProvider dataLocateLayout
   */
  function testLocateLayout($template_file, $expected_file, $files) {
    foreach ($files as $file) {
      $this->TFM->newFile($file, '');
    }
    $this->assertEquals(
      $this->TFM->getPath($expected_file),
      $this->RT->locateLayoutFor($template_file)
    );
  }
  
  function dataLocateLayout() {
    return array(
      array(
        'Representation/AResource/Go.GET.html.php',
        'Representation/Layout.html.php',
        array('Representation/Layout.html.php')
      ),
      array(
        'Representation/Index.POST.html.php',
        'Representation/Layout.html.php',
        array('Representation/Layout.html.php')
      ),
      array(
        'Representation/Index.GET.xml.php',
        'Representation/Layout.xml.php',
        array('Representation/Layout.html.php', 'Representation/Layout.xml.php')
      ),
      array(
        'Representation/Index.POST.html.haml',
        'Representation/Layout.html.haml',
        array(
          'Representation/Layout.html.haml', 'Representation/Layout.html.php'
        )
      ),
    );
  }
  
  function testReturnsFalseWhenNoLayoutIsFoundForRequest() {
    $this->assertSame(
      FALSE, $this->RT->locateLayoutFor('Foofile')
    );
  }
  
  /**
   * @dataProvider dataMimeType
   */
  function testMimeType($template_file, $expected_type) {
    $this->assertEquals(
      $expected_type, $this->RT->getMimeTypeFor($template_file)
    );
  }
  
  function dataMimeType() {
    return array(
      array(
        'Representation/AResource/Go.GET.html.php', 'text/html'
      ),
      array(
        'Representation/Index.POST.xml.php', 'application/xml'
      ),
      array(
        'Representation/Index.GET.xhtml.php', 'application/xhtml+xml'
      ),
      array(
        'Representation/Index.POST.json.haml', 'application/json',
      ),
      array(
        'Representation/Index.GET.js.php', 'text/javascript',
      ),
      array(
        'Representation/Index.GET.css.php', 'text/css',
      ),
    );
  }
  
}
