<?php

namespace Asar;


/**
 * This is a modified version of the SplClassLoader. The modification now allows
 * for loading single classes (e.g. Pimple.php).
 *
 * @author Wayne Duran <asartalo@projectweb.ph>
 *
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/final-proposal
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 */
class ClassLoader {

  private $_fileExtension = '.php';
  private $_namespace;
  private $_includePath;
  private $_namespaceSeparator = '\\';

  /**
   * Creates a new <tt>SplClassLoader</tt> that loads classes of the
   * specified namespace.
   * 
   * @param string $ns The namespace to use.
   */
  function __construct($namespace = null, $includePath = null) {
    $this->_namespace = $namespace;
    $this->_includePath = $includePath;
  }

  /**
   * Sets the namespace separator used by classes in the namespace of this class loader.
   * 
   * @param string $sep The separator to use.
   */
  function setNamespaceSeparator($sep) {
    $this->_namespaceSeparator = $sep;
  }

  /**
   * Gets the namespace seperator used by classes in the namespace of this class loader.
   *
   * @return void
   */
  function getNamespaceSeparator() {
    return $this->_namespaceSeparator;
  }

  /**
   * Sets the base include path for all class files in the namespace of this class loader.
   * 
   * @param string $includePath
   */
  function setIncludePath($includePath) {
      $this->_includePath = $includePath;
  }

  /**
   * Gets the base include path for all class files in the namespace of this class loader.
   *
   * @return string $includePath
   */
  function getIncludePath() {
    return $this->_includePath;
  }

  /**
   * Sets the file extension of class files in the namespace of this class loader.
   * 
   * @param string $fileExtension
   */
  function setFileExtension($fileExtension) {
    $this->_fileExtension = $fileExtension;
  }

  /**
   * Gets the file extension of class files in the namespace of this class loader.
   *
   * @return string $fileExtension
   */
  function getFileExtension() {
      return $this->_fileExtension;
  }

  /**
   * Installs this class loader on the SPL autoload stack.
   */
  function register() {
    spl_autoload_register(array($this, 'loadClass'));
  }

  /**
   * Uninstalls this class loader from the SPL autoloader stack.
   */
  function unregister() {
    spl_autoload_unregister(array($this, 'loadClass'));
  }

  /**
   * Loads the given class or interface.
   *
   * @param string $className The name of the class to load.
   * @return void
   */
  function loadClass($className) {
    if (
      null === $this->_namespace ||
      $this->_namespace == $className ||
      $this->_namespace.$this->_namespaceSeparator ===
        substr(
          $className, 0, strlen(
            $this->_namespace.$this->_namespaceSeparator
          )
        )
    ) {
      $fileName = '';
      $namespace = '';
      if ($className == $this->_namespace) {
        $fileName .= $className . $this->_fileExtension;
      } elseif (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
      }
      $file = ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
      if (!file_exists($file)) {
        return false;
      }
      include_once $file;
      return true;
    }
  }
}
