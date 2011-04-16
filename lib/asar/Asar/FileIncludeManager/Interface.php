<?php
/**
 * A wrapper interface for including files
 * @todo: maybe this can be removed in favor of SplClassLoader
 *
 * @package Asar
 * @subpackage core
 */
interface Asar_FileIncludeManager_Interface {
  function requireFileOnce($file);
  function includeFile($file);
}
