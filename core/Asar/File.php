<?php
/**
 * A wrapper class for simplifying file creation and access
 * 
 * EXAMPLE - File Creation 
 * The following code creates a file named 'filename.ext'
 * with the content 'Hello World!' and saves it.
 * 
 *   Asar_File::create('filename.ext')->write('Hello World!')->save();
 * 
 * The following code creates a file named 'filename.ext'
 * in the 'path/' directory ('this directory must exist'),
 * writes the content 'Hello Again!' and saves it.
 * 
 *   Asar_File::create('path/filename.ext')->write('Hello Again!')->save();
 * 
 * 
 * EXAMPLE - Opening a File
 * 
 * The following gets the contents of a file:
 * 
 *   $contents = Asar_File::open('thefile.ext')->read(); 
 * 
 * The following opens a file, writes a content on it, and then saves it:
 * 
 *   $f = Asar_File::open('tehfile.ext')->write($thecontentstring)->save();
 * 
 * 
 * The static methods open and create are wrappers for the 
 * constructor method
 * 
 * 
 * Created on Jul 2, 2007
 * 
 * @author     Wayne Duran
 * @todo       Changing File Mode (chmod?)
 * @todo       Making sure we point to the right file
 */

//require_once 'Base.php';

class Asar_File {
  
  private $filename           = NULL;
  private $content            = NULL;
  private $resource           = NULL;
  private $mode               = 'a+b';
  private $forced_append_mode = FALSE;
  
  
  public static function create ($filename) {
    if (file_exists($filename)) {
      throw new Asar_File_Exception(
        "Asar_File::create failed. The file '$filename' already exists."
      );
    }
    $f = new self($filename, 'w+b');
    return $f; 
  }
  
  public static function open ($filename) {
    if (!file_exists((string) $filename)) {
      throw new Asar_File_Exception(
        "Asar_File::open failed. The file '$filename' does not exist."
      );
    } else {
      $f = new self($filename, 'r+b');
      return $f;
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
        throw new Asar_File_Exception(
          'Asar_File::getResource failed. The file object ' .
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
      throw new Asar_File_Exception(
        'Asar_File::setFileName failed. Filename should be a non-empty string.'
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
  
  function writeAfter($content) {
    return $this->write($this->getContent().$content);
  }
  
  function read() {
    return $this->getContent();
  }
  
  function save() {
    $test = fwrite($this->getResource(), $this->getContent());
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

