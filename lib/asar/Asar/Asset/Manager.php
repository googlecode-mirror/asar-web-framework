<?php

namespace Asar\Asset;

class Manager {
  
  private
    $css = array(),
    $js = array();
  
  function includeCss($file, $options = array()) {
    $this->css[$file] = new Css($file, $options);
  }
  
  function includeJs($file, $options = array()) {
    $this->js[$file] = new Js($file, $options);
  }
  
  function getCssAssets() {
    return $this->sort($this->css);
  }
  
  function getJsAssets() {
    return $this->sort($this->js);
  }
  
  private function sort($assets) {
    $sorted = $visited = array();
	  foreach ($assets as $asset) {
		  $this->visit($asset, $assets, $visited, $sorted);
	  }
	  return $sorted;
  }
  
  private function visit($asset, &$assets, &$visited, &$sorted) {
	  if (!isset($visited[$asset->getPath()])) {
		  $visited[$asset->getPath()] = $asset;
		  foreach ($asset->getDependencies() as $parent) {
		    if (isset($assets[$parent])) {
  			  $this->visit($assets[$parent], $assets, $visited, $sorted);
			  }
		  }
		  $sorted[] = $asset;
	  }
  }
  
}

