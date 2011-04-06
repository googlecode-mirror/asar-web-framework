<?php

require_once realpath(dirname(__FILE__). '/../../../../config.php');

class Asar_Logger_DefaultTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->log_file = $this->getMock(
      'Asar_File', array('writeAfter', 'save')
    );
    $this->logger = new Asar_Logger_Default($this->log_file);
    // Matches with [Thu, 11 Oct 2007 12:38:29 GMT]
    $this->date_time_format = '\[[MTWFS][ouehra][neduit], [0-3][0-9] ' .
      '[A-Za-z]{3} [0-9]{4} [0-2][0-9]:[0-6][0-9]:[0-6][0-9] GMT\]';
  }
  
  function testLoggerAppendsToTheLogFile() {
    $this->log_file->expects($this->once())
      ->method('writeAfter')
      ->with(
        $this->matchesRegularExpression(
          "/^{$this->date_time_format} Foo bar.\\n/"
        )
      );
    $this->logger->log('Foo bar.');
  }
  
  function testLoggerSavesFileAfterWriting() {
    $this->log_file->expects($this->at(0))
      ->method('writeAfter');
    $this->log_file->expects($this->at(1))
      ->method('save');
    $this->logger->log('Foo.');
  }
  
  function testGettingLogFileObject() {
    $this->assertSame($this->log_file, $this->logger->getLogFile());
  }

}
