<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Controller_NavigatorTest extends Asar_Test_Helper {
    function testInstantiatingNavigator() {
        $controller_sample = $this->getMock('Some_Controller', array(), array(), '', false, false, false);
        $navigator = Asar_Controller_Navigator::getNavigator($controller_sample);
        $this->assertTrue($navigator instanceof Asar_Controller_Navigator, 'Navigator not instantiated');
    }
    
    function testFindingFooController() {
        $controller_sample = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index', false, false, false);
        $navigator = Asar_Controller_Navigator::getNavigator($controller_sample);
        $this->assertEquals('Some_Controller_Foo', $navigator->find('Foo'), 'Navigator was not able to find proper conroller name');
    }
    
    function testFindingBarController() {
        $controller_sample = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index2', false, false, false);
        $navigator = Asar_Controller_Navigator::getNavigator($controller_sample);
        $this->assertEquals('Some_Controller_Bar', $navigator->find('Bar'), 'Navigator was not able to find proper conroller name');
    }
    
    function testFindingADifferentKindOfController() {
        $controller_sample = $this->getMock('Some1_Controller', array(), array(), 'Some1_Controller_Index', false, false, false);
        $navigator = Asar_Controller_Navigator::getNavigator($controller_sample);
        $this->assertEquals('Some1_Controller_Foo', $navigator->find('Foo'), 'Navigator was not able to find proper conroller name');
    }
    
    function testMultipleCallsForSameTypeOfContextForANavigatorWillUseTheSameNavigator() {
        $controller_sample1 = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index3', false, false, false);
        $controller_sample2 = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index4', false, false, false);
        $navigator1 = Asar_Controller_Navigator::getNavigator($controller_sample1);
        $navigator2 = Asar_Controller_Navigator::getNavigator($controller_sample2);
        $this->assertSame($navigator1, $navigator2, 'Navigator instances are different');
    }
    
    function testCallsForANavigatorsWithDiferentContextsWillUseTheDifferentNavigators() {
        $controller_sample1 = $this->getMock('Some_Controller', array(), array(), 'Some_Controller_Index5', false, false, false);
        $controller_sample2 = $this->getMock('Some_Controller', array(), array(), 'A_Different_Controller_Index2', false, false, false);
        $navigator1 = Asar_Controller_Navigator::getNavigator($controller_sample1);
        $navigator2 = Asar_Controller_Navigator::getNavigator($controller_sample2);
        $this->assertNotSame($navigator1, $navigator2, 'Navigator instances must be different');
    }
}
