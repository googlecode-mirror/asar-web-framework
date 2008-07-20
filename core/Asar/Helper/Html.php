<?php
/**
 * Asar_Helper_Html class definition - Asar Web Framework Core
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to wayne@asartalo.org so we can send you a copy immediately.
 * 
 * @package   Asar-Core
 * @copyright Copyright (c) 2007-2008, Wayne Duran <wayne@asartalo.org>.
 * @since     0.1
 * @version   $Id$
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.google.com/p/asar-web-framework
 */

/**
 * Asar_Helper_Html
 *
 * Helper class for Html output
 *
 * @package Asar-Core
 */
abstract class Asar_Helper_Html {
	
	/**
	 * Creates an unordered list from an array
	 *
	 * Each element in the array will be an element
	 * of the list.
	 * 
	 * @param array array an array of elements
	 * @return string An HTML unordered list
	 */
    static function uList(array $array) {
        $list = '<ul>';
        foreach ($array as $value) {
            $list .= '<li>'.htmlentities($value).'</li>';
        }
        return $list.'</ul>';
    }
}