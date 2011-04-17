<?php
namespace Asar;

use \Asar\TemplatePackageProvider;
use \Asar\TemplateSimpleRenderer;
use \Asar\Response\ResponseInterface;
use \Asar\Request\RequestInterface;
use \Asar\Template\TemplateInterface;

/**
 * @package Asar
 * @subpackage core
 */
class TemplateRenderer {
  
  private $factory, $renderer;
  
  function __construct(
    TemplatePackageProvider $factory, TemplateSimpleRenderer $renderer
  ) {
    $this->factory = $factory;
    $this->renderer = $renderer;
  }
  
  function renderFor(
    $resource_name, ResponseInterface $response, RequestInterface $request
  ) {
    $templates = $this->factory->getTemplatesFor($resource_name, $request);
    if (!$templates['template'] instanceof TemplateInterface) {
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
