<?php
namespace Asar\Template;

use Asar\Configurable\ConfigurableInterface;
/**
 */
interface TemplateInterface extends ConfigurableInterface {
  
  function setTemplateFile($file);
  function getTemplateFile();
  function getLayoutVars();
  function render($vars=array());
  
}
