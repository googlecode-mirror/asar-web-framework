<?php
namespace Asar\Utility\Cli;

use \Asar\Utility\Cli;

/**
 */
interface CliInterface {
  function setController(Cli $controller);
  function getTaskNamespace();
}
