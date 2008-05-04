<?php
/**
 * Html Template Class
 * 
 * This template is called whenever an HTML representation
 * is needed for a certain resources. Use this to create
 * HTML-based templates
 *
 **/
class Asar_Template_Html extends Asar_Template 
{
    private $layout_path = null;
    private $layout      = null;
    
    /**
     * Sets the layout to use for this template
     *
     * A layout is a template that wraps around
     * another template.
     *
     * @param string $layout_path a path to the layout template
     * @return void
     **/
    public function setLayout($layout_path)
    {
        $this->layout_path = $layout_path;
    }
    
    /**
     * Returns the path set using setLayout
     *
     * @return path string
     **/
    public function getLayout()
    {
        return $this->layout_path;
    }
    
    /**
     * Outputs the template with the values
     * 
     * Outputs the template as a string where the
     * value of the template variables inside the template???
     * @todo write a better description
     *
     * @return string the template output
     **/
    public function fetch($_file = null)
    {
        $contents = parent::fetch($_file);
        if ($this->layout_path === null) {
            return $contents;
        } else {
            $layout = new Asar_Template_Html;
            $layout['contents'] = $contents;
            return $layout->fetch($this->layout_path);
        }
    }
} // END class Asar_Template_Html extends Asar_Template