<?php

abstract class Asar_Navigator {
    abstract static function getNavigator($context);
    abstract function find($name);
}