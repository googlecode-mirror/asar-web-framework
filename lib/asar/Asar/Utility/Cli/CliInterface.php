<?php
namespace Asar\Utility\Cli;

use \Asar\Utility\Cli;

/**
 * @package Asar
 * @subpackage core
 */
interface CliInterface {
  function setController(Cli $controller);
  function getTaskNamespace();
}
