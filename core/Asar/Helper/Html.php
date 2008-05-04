<?php

abstract class Asar_Helper_Html {
    static function uList(array $array)
    {
        $list = '<ul>';
        foreach ($array as $value) {
            $list .= '<li>'.htmlentities($value).'</li>';
        }
        return $list.'</ul>';
    }
}