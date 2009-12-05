<?php
    
interface Asar_Template_Interface {

    public function render();
    
    public function __set($variable, $value = null);
    
    public function set($variable, $value = null);
    
    public function setLayout($layout_file);
    
    public function setTemplateFile($file);
    
    public function getTemplateFile();
    
    public function getTemplateFileExtension();
}
