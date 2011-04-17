<?php
namespace Asar;

use \Asar\File\Exception\FileAlreadyExists;
use \Asar\File\Exception\DirectoryNotFound;
use \Asar\File\Exception\FileDoesNotExist;
use \Asar\File\Exception as FileException;

/**
 * A wrapper class for simplifying file creation and access
 * 
 * EXAMPLE - File Creation 
 * The following code creates a file named 'filename.ext'
 * with the content 'Hello World!' and saves it.
 * 
 * <code>
 *   Asar\File::create('filename.ext')->write('Hello World!')->save();
 * </code>
 * 
 * The following code creates a file named 'filename.ext'
 * in the 'path/' directory ('this directory must exist'),
 * writes the content 'Hello Again!' and saves it.
 * 
 * <code>
 *   Asar\File::create('path/filename.ext')->write('Hello Again!')->save();
 * </code>
 * 
 * 
 * EXAMPLE - Opening a File
 * 
 * The following gets the contents of a file:
 * 
 * <code>
 *   $contents = Asar\File::open('thefile.ext')->read(); 
 * </code>
 * 
 * The following opens a file, writes a content on it, and then saves it:
 * 
 * <code>
 *   $f = Asar\File::open('tehfile.ext')->write($thecontentstring)->save();
 * </code>
 * 
 * 
 * The static methods {@link open()} and {@link create()} are wrappers for the 
 * constructor method
 * 
 * 
 * Created on Jul 2, 2007
 * 
 * @author     Wayne Duran
 * @todo       Changing File Mode (chmod?)
 * @todo       Making sure we point to the right file
 * @package    Asar
 * @subpackage core
 */
class File {
  
  private $filename           = NULL;
  private $content            = NULL;
  private $resource           = NULL;
  private $mode               = 'a+b';
  private $forced_append_mode = FALSE;
  
  
  public static function create ($filename) {
    if (file_exists($filename)) {
      throw new FileAlreadyExists(
        "Asar\File::create failed. The file '$filename' already exists."
      );
    }
    if (!file_exists(dirname($filename))) {
      throw new DirectoryNotFound(
        'Asar\File::create failed. Unable to find the directory to create the '.
        'file to (' . dirname($filename) . ').'
      );
    }
    return new self($filename, 'w+b');
  }
  
  public static function open ($filename) {
    if (!file_exists((string) $filename)) {
      throw new FileDoesNotExist(
        "Asar\File::open failed. The file '$filename' does not exist."
      );
    } else {
      return new self($filename, 'r+b');
    }
  }
  
  public static function unlink ($filename) {
    if (file_exists((string) $filename)) {
      return unlink($filename);
    } else {
      return false;
    }
  }
  
  function __construct($filename = NULL, $mode = 'a+b') {
    if (is_string($filename)) {
      $this->setFileName($filename);
      $this->mode = $mode;
      $this->getResource();
      if (file_exists($this->getFileName())) {
        $this->content = file_get_contents($this->getFileName());
      }
    }
  }
  
  function appendMode() {
    $this->mode = 'a+b';
    $this->unsetResource();
    $this->getResource();
    $this->forced_append_mode = TRUE;
    return $this;
  }
  
  private function getResource() {
    if (!is_resource($this->resource)) {
      // Attempt to create a resource using filename
      if (!$this->getFileName()) {
        throw new FileException(
          'Asar\File::getResource failed. The file object ' .
          'does not have a file name.'
        );
      }
      $this->resource = fopen($this->filename, $this->mode);
    }
    return $this->resource;
  }
  
  private function unsetResource() {
    if (is_resource($this->resource)) {
      fclose($this->resource);
    }
  }
  
  function setFileName($filename) {
    if (!is_string($filename) || $filename === '') {
      throw new FileException(
        'Asar\File::setFileName failed. Filename should be a non-empty string.'
      );
    }
    $this->filename = $filename;
  }

  function getFileName() {
    return $this->filename;
  }
  
  function setContent($content) {
    if (is_array($content)) {
      $content = implode("\n", $content);
    }
    $this->content = (string) $content;
  }
  
  function getContent() {
    return $this->content;
  }
  
  function getContents() {
    return $this->getContent();
  }
  
  function write($content) {
    $this->setContent($content);
    return $this;
  }
  
  function writeBefore($content) {
    return $this->write($content.$this->getContent());
  }
  
  /**
   * TODO: Optimize for append_mode
   */
  function writeAfter($content) {
    return $this->write($this->getContent().$content);
  }
  
  function read() {
    return $this->getContent();
  }
  
  function save() {
    fwrite($this->getResource(), $this->getContent());
    if (!$this->forced_append_mode) {
      $this->unsetResource();
    }
    return $this;  
  }
  
  function delete() {
    $this->unsetResource();
    return unlink($this->getFileName());
  }
  
  function __destruct() {
    $this->unsetResource();
  }
}

