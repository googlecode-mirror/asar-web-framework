<?php
/**
 * Asar_Helper_String - Asar Web Framework Core
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
 * Asar_Helper_String
 *
 * Helper class for manipulating strings
 *
 * @package Asar-Core
 */
abstract class Asar_Helper_String {
	
	/**
	 * A function that converts an underscored or dashed string to camelCase.
	 *
	 * For example, when passed 'foo_bar_yeah' or 'foo-bar-yeah' as argument, it will return
	 * 'FooBarYeah'.
	 * @param string str the underscored or dashed words
	 */
	static function camelCase($str) {
        return str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $str)));
    }

	/**
	 * Similar to Asar_Helper_String::camelCase but lower-cases the first character
	 *
	 * @param string str the underscored or dashed string
	 * @see Asar_Helper_String::camelCase()
	 */
	static function lowerCamelCase($str) {
        $str = self::camelCase($str);
        $str[0] = strtolower($str[0]);
        return $str;
    }
	
	/**
	 * Converts a camelCased string to an underscored_string.
	 *
	 * Does the inverse of Asar_Helper_String::camelCase(). When passed
	 * with 'FooBarYeah', it will return 'foo_bar_yeah'.
	 * @param string str the camelCased string to convert
	 * @see  Asar_Helper_String::camelCase()
	 */
	static function underscore($str) {
	    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $str));
    }
	
	/**
	 * Converts a camelCased string to a dashed_string
	 *
	 * Does the same as Asar_Helper_String::underscore() except
	 * that instead of underscores, it uses dashes. hen passed
	 * with 'FooBarYeah', it will return 'foo-bar-yeah'.
	 * @param string str the camelCased string to convert
	 * @see Asar_Helper_String::underscore()
	 */
	static function dash($str) {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '-\\1', $str));
    }
}