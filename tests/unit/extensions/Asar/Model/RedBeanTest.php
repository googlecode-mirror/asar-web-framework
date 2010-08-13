<?php

require_once realpath(dirname(__FILE__). '/../../../../config.php');

/*
interface RedBean_ObjectDatabase {
  public function load( $type, $id );
  public function store( RedBean_OODBBean $bean );
  public function trash( RedBean_OODBBean $bean );
  public function batch( $type, $ids );
  public function dispense( $type );
}
RedBean_OODBBean
 */

// TODO: Figure out if we can use mocking instead of this static class
class Asar_Model_RedBeanTest_DummyBeanModel extends Asar_Model_RedBean {
  
}

// TODO: Maybe it's better if we define a wrapper class for RedBean_OODB
// so that it can do other stuff.
class Asar_Model_RedBeanTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    if (!class_exists('RedBean_OODBBean')) {
      $this->getMock('RedBean_OODBBean');
    }
  }
  
  function testModelInstantiationAndSave() {
    $bean = $this->getMock('RedBean_OODBBean');
    $oodb = $this->getMock('RedBean_ObjectDatabase', array('store'));
    $oodb->expects($this->once())
      ->method('store')
      ->with($this->equalTo($bean));
    //$model = $this->getMock('Asar_Model_RedBean', array(), array($oodb, $bean));
    $model = new Asar_Model_RedBeanTest_DummyBeanModel($oodb, $bean);
    $model->save();
  }
  
  function testModelInstantiationDispensesBeanWhenNoBeanIsPassed() {
    $bean = $this->getMock('RedBean_OODBBean');
    $oodb = $this->getMock('RedBean_ObjectDatabase', array('dispense', 'store'));
    $oodb->expects($this->once())
      ->method('dispense')
      ->with('Asar_Model_RedBeanTest_DummyBeanModel')
      ->will($this->returnValue($bean));
    $oodb->expects($this->once())
      ->method('store')
      ->with($this->equalTo($bean));
    //$model = $this->getMock('Asar_Model_RedBean', array(), array($oodb));
    $model = new Asar_Model_RedBeanTest_DummyBeanModel($oodb);
    $model->save();
  }
  
  function testModelInvokesDefinePropertiesMethodOnConstruction() {
    eval('
      class ' . get_class($this) . '_DummyBeanModel2 extends Asar_Model_RedBean {
        function defineProperties() {
          $GLOBALS["foo"] = "bar";
          return array("foo" => array());
        }
      }
    ');
    $oodb = $this->getMock('RedBean_ObjectDatabase', array('dispense', 'store'));
    $model = new Asar_Model_RedBeanTest_DummyBeanModel2($oodb);
    $this->assertArrayHasKey(
      'foo', $GLOBALS,
      'The method "defineProperties" was not run.'
    );
    $this->assertEquals(
      'bar', $GLOBALS['foo'], 
      'The method "defineProperties" was not run.'
    );
  }
  
  
  
}
