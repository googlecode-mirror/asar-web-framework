<?php
class Asar_Template extends Asar_Base implements ArrayAccess {
	protected $vars = array(); // Holds all the template variables
	protected $path; // Path to the templates
	protected $template_file; // Template file to use
	protected $controller;

	public function getPath() {
		return $this->path;
	}
	
	
	public function setPath($path) {
		$this->path = $path;
	}
	
	
	/**
	 * Set the template to use.
	 */
	public function setTemplate($file) {
		$this->template_file = $file;
	}
	
	public function getTemplate()
	{
		return $this->template_file;
	}
	
	public function setController($controller)
	{
		$this->controller = $controller;
	}
	
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Set a template variable.
	 *
	 * @param mixed $var name of the variable to set or an associative array with 'var_name' => value pairs
	 * @param mixed $value the value of the variable
	 *
	 * @return void
	 */
	public function set($var, $value = NULL) {
		if (is_array($var)) {
			$this->vars = array_merge($this->vars, $var);
		} else {
			$this->vars[$var] = $value;
		}
	}


	/**
	 * Set a bunch of variables at once using an associative array.
	 *
	 * @todo Needs optimization
	 * @param array $vars array of vars to set
	 * @return void
	 */
	public function setVars(array $vars) {
		$this->set($vars);
	}
	
	
	public function getVars() {
		return $this->vars;
	}
	
	/**
	 * Array Access interface implementation.
	 * These methods allow for accessing the template
	 * variables used for displaying by the array
	 * syntax: $tpl['variable_name']; The following
	 * code are equivalent: 
	 * $tpl->set('var', 'value');
	 * $tpl['var'] = 'value';
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->vars);
	}
	
	/**
	 * Array Access interface implementation
	 */
	public function offsetGet($offset) {
		if (array_key_exists($offset, $this->vars)) {
			return $this->vars[$offset];
		} else {
			return NULL;
		}
	}
	
	/**
	 * Array Access interface implementation
	 */
	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}
	
	/**
	 * Array Access interface implementation
	 */
	public function offsetUnset($offset) {
		unset($this->vars[$offset]);
	}
	
	
    /**
     * Adds a file to the list of template files included on the debug
     * messages
     *
     * @param string file the file to be added to the list of templates
     * @todo needs refactoring
     **/
    private static function _logFile($file) {
        $debug_array = Asar::getDebugMessages();
        if (!array_key_exists('Templates', $debug_array)) {
            Asar::debug('Templates', array(realpath(Asar::fileExists($file))));
        } else {
            $debug_array['Templates'][] = realpath(Asar::fileExists($file));
            Asar::debug('Templates', $debug_array['Templates']);
        }
    }

	/**
	 * Open, parse, and return the template file.
	 *
	 * @param string $_file the template file name
	 *
	 * @return string
	 */
	public function fetch($_file = NULL) {
		if (NULL == $_file) {
			$_file = $this->template_file;
		}
		
		if (Asar::fileExists($this->path . $_file)) {
		    if ($this->isDebugMode()) {
		        self::_logFile($_file);
	        }
			extract($this->vars);           // Extract the variables set
			ob_start();						// Start output buffering
			include($this->path . $_file);	// Include the file
			$_contents = ob_get_clean();	// Get the contents of the buffer. End buffering and discard
			return $_contents;				// Return the contents
		} else {
			$this->exception('Template file not found');
			return null;
		}
		
	}
	
	
	public function __toString() {
		return $this->fetch();
	}
}
