<?php
if (isset($_asserts)) {
  $_assets->includeJs('js/foo.js', 'js/bar.js');
  $_assets->includeJs('js/bar.js');
  $_assets->includeCss('css/foo.css');
}
?>
<h1>Javascript and CSS Include Example</h1>
