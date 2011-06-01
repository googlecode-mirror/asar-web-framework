<?php
if (isset($_helpers) && $assets = $_helpers['assets']) {
  $assets->includeJs('js/foo.js', 'js/bar.js');
  $assets->includeJs('js/bar.js');
  $assets->includeCss('css/foo.css');
}
?>
<h1>Javascript and CSS Include Example</h1>
