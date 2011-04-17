<?php
namespace Asar;

use \Asar\Template\TemplateInterface;

/**
 * @package Asar
 * @subpackage core
 */
class TemplateSimpleRenderer {
  
  function renderTemplate(
    $template, $vars,
    $layout = null
  ) {
    if (!$template instanceof TemplateInterface) {
      return null;
    }
    $output = $template->render($vars);
    if (
      !$template->getConfig('no_layout') && 
      $layout instanceof TemplateInterface
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
