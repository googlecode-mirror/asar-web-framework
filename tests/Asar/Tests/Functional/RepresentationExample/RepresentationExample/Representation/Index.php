<?php

namespace Asar\Tests\Functional\RepresentationExample\RepresentationExample\Representation;

class Index extends \Asar\Representation {
  
  function getHtml($data) {
    return 
      '<html>
        <head>
          <title>Representation Example Index</title>
        </head>
        <body>
          <h1>'. $data['h1'] . '</h1>
          <p>'. $data['p'] . '</p>
        </body>
      </html>';
  }
  
  function getTxt($data) {
    return "-------\n{$data['h1']}\n-------\n\n{$data['p']}";
  }
  
  function getXml($data) {
    $template = new \Asar\Template\Engines\PhpEngine;
    $template->setTemplateFile(
      __DIR__ . DIRECTORY_SEPARATOR . 'index_xml_template.php'
    );
    return $template->render($data);
  }
  
}
