<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Controller_LocatorTest extends Asar_Test_Helper {
    function testInstantiatingLocator() {
        $controller_sample = $this->getMock('Some_Controller', array(), array(), '', false, false, false);
        $locator = Asar_Controller_Locator::getLocator($controller_sample);
        $this->assertTrue($locator instanceof Asar_Controller_Locator, 'Locator not instantiated');
    }
    
    function testFindingFooController() {
        $controller_sample = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index', false, false, false);
        $locator = Asar_Controller_Locator::getLocator($controller_sample);
        $this->assertEquals('Some_Controller_Foo', $locator->find('Foo'), 'Locator was not able to find proper conroller name');
    }
    
    function testFindingBarController() {
        $controller_sample = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index2', false, false, false);
        $locator = Asar_Controller_Locator::getLocator($controller_sample);
        $this->assertEquals('Some_Controller_Bar', $locator->find('Bar'), 'Locator was not able to find proper conroller name');
    }
    
    function testFindingADifferentKindOfController() {
        $controller_sample = $this->getMock('Some1_Controller', array(), array(), 'Some1_Controller_Index', false, false, false);
        $locator = Asar_Controller_Locator::getLocator($controller_sample);
        $this->assertEquals('Some1_Controller_Foo', $locator->find('Foo'), 'Locator was not able to find proper conroller name');
    }
    
    function testMultipleCallsForSameTypeOfContextForALocatorWillUseTheSameLocator() {
        $controller_sample1 = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index3', false, false, false);
        $controller_sample2 = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index4', false, false, false);
        $locator1 = Asar_Controller_Locator::getLocator($controller_sample1);
        $locator2 = Asar_Controller_Locator::getLocator($controller_sample2);
        $this->assertSame($locator1, $locator2, 'Locator instances are different');
    }
    
    function testCallsForALocatorsWithDiferentContextsWillUseTheDifferentLocators() {
        $controller_sample1 = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index5', false, false, false);
        $controller_sample2 = $this->getMock('Some_Controller', array(), array(), 'A_Different_Controller_Index2', false, false, false);
        $locator1 = Asar_Controller_Locator::getLocator($controller_sample1);
        $locator2 = Asar_Controller_Locator::getLocator($controller_sample2);
        $this->assertNotSame($locator1, $locator2, 'Locator instances must be different');
    }
}
