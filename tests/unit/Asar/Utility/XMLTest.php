<?php
require_once realpath(dirname(__FILE__) . '/../../../config.php');



class Asar_Utility_XMLTest extends PHPUnit_Framework_TestCase
{
  const XMLSTR = '
<html>
<head>
  <title>This is an example HTML document</title>
</head>
<body>
  <div id="wrapper">
    <h1>The Main Heading</h1>
    <p>An example of a very serious paragraph with<br />
      a line break.
    </p>
    <h2>The following is a complicated list</h2>
    <ul id="foo">
      <li>One</li>
      <li>Two</li>
      <li>
        <ul>
          <li>Three - One</li>
          <li>Three - Two</li>
        </ul>
      </li>
      <li>
        Four
        <ul>
          <li>Four - One</li>
          <li id="bar">Four - Two</li>
        </ul>
      </li>
      <li>Five</li>
    </ul>
    <img src="/img.gif" alt="something" />
  </div>
</body>
</html>
';  
  
  public function setUp()
  {
    $this->xml = new Asar_Utility_XML(self::XMLSTR);
  }
  
  function testGettingStringValue() {
      $this->assertEquals(
        'This is an example HTML document',
        $this->xml->head->title->stringValue(),
        'Unable to get string value of xml title element.'
      );
  }
  
  function testGettingNestedElementValue() {
    $this->assertEquals(
      'Three - One',
      $this->xml->body->div->ul->li[2]->ul->li->stringValue(),
      'Unable to get string value of a deeply nested li element.'
    );
  }
  
  function testGettingElementById() {
    $this->assertEquals(
      'Four - Two',
      $this->xml->getElementById('bar')->stringValue(),
      'Unable to get element by id.'
    );
  }
  
  function testGettingElementByIdNotFound() {
    $this->assertSame(
      null, $this->xml->getElementById('baz')
    );
  }
  
}
