<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_TemplateRenderer {
  
  private $factory, $renderer;
  
  function __construct(
    Asar_TemplatePackageProvider $factory,
    Asar_TemplateSimpleRenderer $renderer
  ) {
    $this->factory = $factory;
    $this->renderer = $renderer;
  }
  
  function renderFor(
    $resource_name, Asar_Response $response, Asar_Request $request
  ) {
    $templates = $this->factory->getTemplatesFor($resource_name, $request);
    if (!$templates['template'] instanceof Asar_Template_Interface) {
      $response->setStatus(406);
    } else {
      $response->setContent(
        $this->renderer->renderTemplate(
          $templates['template'], $response->getContent(), $templates['layout']
        )
      );
      $response->setHeader('Content-Type', $templates['mime-type']);
    }
    return $response;
  }
  
  private function getLayoutVars($content, $tpl) {
    $others = $tpl->getLayoutVars();
    if (is_array($others)) {
      return array_merge(array('content' => $content), $others);
    }
    return array('content' => $content);
  }
}
