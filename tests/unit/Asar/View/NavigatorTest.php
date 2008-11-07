<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_View_NavigatorTest extends Asar_Test_Helper {
    
    function testGettingNavigator() {
        $controller = $this->getMock('Asar_Controller', array(), array(), '', false, false, false);
        $navigator = Asar_View_Navigator::getNavigator($controller);
        $this->assertTrue($navigator instanceof Asar_View_Navigator, 'Unable to get view navigator');
    }
    
}


