<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Helper_HtmlTest extends PHPUnit_Framework_TestCase {
    
    function testCreatingAnUnorderedList()
    {
        $arr = array('One', 'Two', 'Three', 'Four');
        $list = simplexml_load_string(Asar_Helper_Html::uList($arr));
        $this->assertEquals('ul', $list->getName(), 'uList did not return a string that has an opening and closing ul tag');
        $list_items = $list->children();
        $this->assertEquals(4, count($list->children()), 'list must have 4 elements');
		reset($arr);
        foreach ($list->li as $item) {
            $this->assertEquals('li', $item->getName(), 'uList did not return create list elements (li)');
            $this->assertEquals(current($arr), $item . '', 'uList did not set the list elements\' values properly;');
            next($arr);
        }
    }
    
    function testCreatingUnorderedListMustMakeSureValuesAreProperlyEncoded() {
        $list = Asar_Helper_Html::uList(array('<>&'));
        $this->assertContains('<li>&lt;&gt;&amp;', $list, 'uList did not html encode values');
    }
}