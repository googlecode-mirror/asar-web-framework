<?php

namespace Asar\Tests\Functional\DebuggingExample;

require_once realpath(__DIR__ . '/../../../../') . '/config.php';

use \Asar\Client;
use \Asar\ApplicationInjector;
use \Asar\ApplicationScope;
use \Asar\Config\DefaultConfig;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

class Test extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->client = new Client;
    $this->app = ApplicationInjector::injectApplication(
      new ApplicationScope(
        'Asar\Tests\Functional\DebuggingExample\DebuggingExample',
        new DefaultConfig
      )
    );
  }
  
  function testDebugInformationIsPresent() {
    $resource = $this->client->GET($this->app);
    $this->assertEquals(200, $resource->getStatus(), $resource->getContent());
    $this->assertTag(
      array(
        'tag' => 'div', 'id' => 'asarwf_debug_info'
      ),
      $resource->getContent()
    );
  }
  
  function testDebugInformationTableIsPresent() {
    $this->assertTag(
      array(
        'tag' => 'table', 'parent' => array(
          'tag' => 'div', 'id' => 'asarwf_debug_info'
        )
      ),
      $this->client->GET($this->app)->getContent()
    );
  }
  
  function testDebugInformationContainsExecutionTimeInformation() {
    $content = $this->client->GET($this->app)->getContent();
    $this->assertTag(
      array(
        'tag' => 'th',
        'id'  => 'asarwf_dbgl_execution_time',
        'content' => 'Execution Time'
      ),
      $content,
      "Unale to find execution time in debugging information in \n $content"
    );
    
    $txt_content = $this->findElementContent($content, array(
        'tag' => 'td', 'id' => 'asarwf_dbgv_execution_time'
      )
    );
    $this->assertContains('microseconds', $txt_content);
    if (strpos($txt_content, 'E') > 0) {
      $this->assertRegExp('/[0-9]+(.)?[0-9]*E-[0-9]+ microseconds/', $txt_content);
    } else {
      $this->assertRegExp('/[0-9]+(.)?[0-9]* microseconds/', $txt_content);
    }
  }
  
  private function findElementContent($html, $matcher) {
    $el = \PHPUnit_Util_XML::findNodes(
      dom_import_simplexml(simplexml_load_string($html))->ownerDocument,
      $matcher
    );
    if ($el === false) {
      return false;
    }
    return $el[0]->textContent;
  }
  
  function testMemoryUsedInDebugInformation() {
    $content = $this->client->GET($this->app)->getContent();
    $this->assertContains(
      'Memory Used', $content,
      'Unable to find memory usage label in debug info table.'
    );
    $txt_content = $this->findElementContent($content, array(
      'id' => 'asarwf_dbgv_memory_used'
    ));
    $this->assertRegExp(
      '/[0-9]+.[0-9]{2}(M|K)B/', $txt_content,
      'Unable to find memory usage value in debug info table.'
    );
  }
  
  function testApplicationNameInDebugInformation() {
    $content = $this->client->GET($this->app)->getContent();
    $this->assertContains(
      'Application', $content,
      'Unable to find application name label in debug info table.'
    );
    $txt_content = $this->findElementContent($content, array(
      'id' => 'asarwf_dbgv_application'
    ));
    $this->assertEquals(
      'Asar\Tests\Functional\DebuggingExample\DebuggingExample', $txt_content,
      'Unable to find application name value in debug info table.'
    );
  }
  
  function testResourceNameInDebugInformation() {
    $content = $this->client->GET($this->app)->getContent();
    $this->assertContains(
      'Resource', $content,
      'Unable to find resource label in debug info table.'
    );
    $txt_content = $this->findElementContent($content, array(
      'id' => 'asarwf_dbgv_resource'
    ));
    $this->assertEquals(
      'Asar\Tests\Functional\DebuggingExample\DebuggingExample\Resource\Index',
      $txt_content, 'Unable to find resource name value in debug info table.'
    );
  }
  
  function testTempalatesInDebugInformation() {
    $content = $this->client->GET($this->app)->getContent();
    $this->assertContains(
      'Templates', $content,
      'Unable to find templates label in debug info table.'
    );
    $this->assertTag(
      array(
        'id' => 'asarwf_dbgv_templates',
        'child' => array(
          'tag' => 'ul',
          'children' => array(
            'count' => 2,
            'only'  => array('tag' => 'li')
          ),
          'child' => array(
            'tag' => 'li',
            'content' => realpath(dirname(__FILE__) . '/DebuggingExample/Representation/Index.GET.html.php'),
            'parent' => array(
              'child' => array(
                'tag' => 'li',
                'content' => realpath(dirname(__FILE__) . '/DebuggingExample/Representation/Layout.html.php'),
              )
            )
            
          )
        )
      ),
      $content
    );
  }
  
  
}
