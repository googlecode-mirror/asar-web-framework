<?php

class Asar_Template implements Asar_Template_Interface {
  private $_template_file = null;
  private $_tpl;
  private $_allow_layout = true;
  private $_layout = null;

  function __construct() {
    $this->_tpl = new Asar_View;
  }

  function __set($variable, $value = null) {
    $this->set($variable, $value);
  }

  function set($variable, $value = null) {
    $this->_tpl->set($variable, $value);
  }

  function setTemplateFile($file) {
    $this->_template_file = $file;
    $this->_tpl->setTemplate($this->_template_file);
  }

  function getTemplateFile() {
    return $this->_template_file;
  }

  function setLayout($layout_file) {
    if (Asar::fileExists($layout_file)) {
      $this->getLayout()->setTemplateFile($layout_file);
    }
  }

  function getLayout() {
    if (!$this->_layout)
      $this->_layout = new self;
    return $this->_layout;
  }

  function noLayout() {
    $this->_allow_layout = false;
  }

  function render() {
    if ($this->_layout && $this->_allow_layout) {
      $this->_layout->set('content', $this->_tpl->fetch());
      return $this->_layout->render();
    }
    try {
      return $this->_tpl->fetch();
    } catch (Asar_View_Exception_FileNotFound $e) {
      throw new Asar_Template_Exception_FileNotFound(
        "Asar_Template::render() failed. The file '{$this->_template_file}' " .
        'does not exist.'
      );
    }
  }

  function getTemplateFileExtension() {
    return 'php';
  }

}
