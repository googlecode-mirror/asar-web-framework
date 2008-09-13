<?php

interface Asar_View_Interface {
    function __set($variable, $value = null);
    function setTemplate($file);
    function set($variable, $value = null);
    function render();
}
