<?php
class Asar_Template implements ArrayAccess {
	protected $vars = array(); // Holds all the template variables
	protected $path; // Path to the templates
	protected $template_file; // Template file to use
	private static $helpers = array();
	private static $helper_methods = array();
	
	
	public function __construct() {
		
	}
	

	/**
	 * Method that adds functionality to the template object.
	 * Accepts a valid defined class name. Public static
	 * methods defined in the class are added to the object's
	 * method list and will be available to the calling object
	 * as if it was defined as public function
	 */
	public static function registerHelper($class) {
		if (!class_exists($class))
			return false;
		
		$reflector = new ReflectionClass($class);
		
		foreach ($reflector->getMethods() as $method) {
			if ($method->isStatic() && $method->isPublic()) {
				self::$helper_methods[$method->getName()] = $method;
			}
		}
		self::$helpers[] = $class;
		return true;
	}
	
	/**
	 * Flush the list of helper methods. Use with caution.
	 * The effect of executing this method is undoable.
	 * You need to re-register the Helper methods again
	 * to use those methods
	 */
	public static function clearHelperRegistry() {
		self::$helpers = array();
		self::$helper_methods = array();
	}
	
	public function __call($name, $arguments) {
		if (array_key_exists($name, self::$helper_methods)) {
			return self::$helper_methods[$name]->invokeArgs(NULL, $arguments);
		} else {
			throw new Asar_Template_Exception('Undefined method "'.$name.'" or the helper method was not registered');
			return NULL;
		}
	}
	
	
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

	/**
	 * Set a template variable.
	 *
	 * @param string $name name of the variable to set
	 * @param mixed $value the value of the variable
	 *
	 * @return void
	 */
	public function set($name, $value) {
		$this->vars[$name] = $value;
	}


	/**
	 * Set a bunch of variables at once using an associative array.
	 *
	 * @todo Needs optimization
	 * @param array $vars array of vars to set
	 * @return void
	 */
	public function setVars($vars, $clear = false) {
		foreach ($vars as $var => $val) {
			$this->set($var, $val);
		}
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
	 * Open, parse, and return the template file.
	 *
	 * @param string string the template file name
	 *
	 * @return string
	 */
	public function fetch($_file = NULL) {
		if (NULL == $_file) {
			$_file = $this->template_file;
		}
		
		extract($this->vars);           // Extract the variables set
		ob_start();						// Start output buffering
		include($this->path . $_file);	// Include the file
		$_contents = ob_get_contents();	// Get the contents of the buffer
		ob_end_clean();					// End buffering and discard
		return $_contents;				// Return the contents
		
	}
	
	
	public function __toString() {
		return $this->fetch();
	}
}

class Asar_Template_Exception extends Exception {}
?>
