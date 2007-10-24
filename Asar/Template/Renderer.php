<?php
class Asar_Template_Renderer  extends Asar_Base {
  private $template_params = array();
  
  function setTemplateParams(array $params) {
    $this->template_params = $params;
  }
  
  function getTemplateParams() {
    return $this->template_params;
  }
}


?>