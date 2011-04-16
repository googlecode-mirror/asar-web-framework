<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_TemplateSimpleRenderer {
  
  function renderTemplate(
    $template, $vars,
    $layout = null
  ) {
    if (!$template instanceof Asar_Template_Interface) {
      return null;
    }
    $output = $template->render($vars);
    if (
      !$template->getConfig('no_layout') && 
      $layout instanceof Asar_Template_Interface
    ) {
      $output = $layout->render($this->extractLayoutVars($output, $template));
    }
    return $output;
  }
  
  private function extractLayoutVars($content, $tpl) {
    $others = $tpl->getLayoutVars();
    if (is_array($others)) {
      return array_merge(array('content' => $content), $others);
    }
    return array('content' => $content);
  }
  
}
