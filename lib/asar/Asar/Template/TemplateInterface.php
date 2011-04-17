<?php
namespace Asar\Template;

use Asar\Configurable\ConfigurableInterface;
/**
 * @package Asar
 * @subpackage core
 */
interface TemplateInterface extends ConfigurableInterface {
  
  function setTemplateFile($file);
  function getTemplateFile();
  function getLayoutVars();
  function render($vars=array());
  
}
