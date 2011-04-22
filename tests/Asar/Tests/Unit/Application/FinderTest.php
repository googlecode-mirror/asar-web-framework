<?php

namespace Asar\Tests\Unit\Application {

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use Asar\Application\Finder;

class FinderTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->app_finder = new Finder;
  }
  
  function testFindReturnsDirectoryOfTheFileWhereApplicationWasDefined() {
    $this->assertEquals(
      __DIR__, $this->app_finder->find('Asar\Tests\Unit\Application\App1')
    );
  }
  
  function testFindReturnsDirectoryOfTheFileWhereConfigWasDefined() {
    $this->assertEquals(
      __DIR__, $this->app_finder->find('Asar\Tests\Unit\Application\App2')
    );
  }
  
  function testFindThrowsExceptionWhenNoConfigOrAppClassesAreDefinedForApp() {
    $appname = 'Asar\Tests\Unit\Application\EmptyApp';
    $this->setExpectedException(
      'Asar\Application\Finder\Exception',
      "Unable to find the app named '$appname'. It could be that no " .
      'Application or Config class was defined in the app directory.'
    );
    $this->app_finder->find($appname);
  }

}

}

namespace Asar\Tests\Unit\Application\App1 {

  class Application extends \Asar\Application {}

}

namespace Asar\Tests\Unit\Application\App2 {

  class Config extends \Asar\Config {}

}

namespace Asar\Tests\Unit\Application\EmptyApp {}
