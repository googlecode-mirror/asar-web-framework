<?php

interface Asar_Template_Interface extends Asar_Configurable_Interface {
  
  function setTemplateFile($file);
  function getTemplateFile();
  function getLayoutVars();
  function render($vars=array());
  
}