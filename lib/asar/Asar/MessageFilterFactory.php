<?php
namespace Asar;

use \Asar\Config\ConfigInterface;
use \Asar\Debug;
use \Asar\DebugPrinter\Html as HtmlDebugPrinter;

/**
 * @package Asar
 * @subpackage core
 */
class MessageFilterFactory {
  
  private $config, $debug, $filters = array();
  
  function __construct(ConfigInterface $config, Debug $debug) {
    $this->config = $config;
    $this->debug  = $this->debug = $debug;
  }
  
  function getFilter($filter_name) {
    if (!isset($this->filters[$filter_name])) {
      if ($filter_name == 'Asar\MessageFilter\Development') {
        $filter = new $filter_name($this->config, $this->debug);
        $filter->setPrinter('html', new HtmlDebugPrinter);
        $this->filters[$filter_name] = $filter;
      } else {
        $this->filters[$filter_name] = new $filter_name($this->config);
      }
    }
    return $this->filters[$filter_name];
  }
  
}
