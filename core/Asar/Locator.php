<?php

abstract class Asar_Locator {
    abstract static function getLocator($context);
    abstract function find($name);
}
