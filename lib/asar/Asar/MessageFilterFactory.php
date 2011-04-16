<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_MessageFilterFactory {
  
  private $config, $debug, $filters = array();
  
  function __construct(Asar_Config_Interface $config, Asar_Debug $debug) {
    $this->config = $config;
    $this->debug  = $this->debug = $debug;
  }
  
  function getFilter($filter_name) {
    if (!isset($this->filters[$filter_name])) {
      if ($filter_name == 'Asar_MessageFilter_Development') {
        $filter = new $filter_name($this->config, $this->debug);
        $filter->setPrinter('html', new Asar_DebugPrinter_Html);
        $this->filters[$filter_name] = $filter;
      } else {
        $this->filters[$filter_name] = new $filter_name($this->config);
      }
    }
    return $this->filters[$filter_name];
  }
  
}
