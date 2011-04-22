<?php
namespace Asar\FileIncludeManager;

/**
 * A wrapper interface for including files
 * @todo: maybe this can be removed in favor of SplClassLoader
 */
interface FileIncludeManagerInterface {
  function requireFileOnce($file);
  function includeFile($file);
}
