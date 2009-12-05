<?php
class Asar_View {
	protected $vars = array(); // Holds all the template variables
	protected $template_file; // Template file to use

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
     * Adds a file to the list of template files included on the debug
     * messages
     *
     * @param string file the file to be added to the list of templates
     * @todo needs refactoring
     **/
    /*
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
		
		if (Asar::fileExists($_file)) {
			extract($this->vars);           // Extract the variables set
			ob_start();						// Start output buffering
			include($_file);	// Include the file
			$_contents = ob_get_clean();	// Get the contents of the buffer. End buffering and discard
			return $_contents;				// Return the contents
		} else {
			throw new Asar_View_Exception_FileNotFound ( 
			    'Asar_View::fetch failed. ' .
			    "Unable to find the template file '$_file'." 
		    );
			return null;
		}
		
	}
	
	
	public function __toString() {
		return $this->fetch();
	}
}
