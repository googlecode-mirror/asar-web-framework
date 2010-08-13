<?php

class RepresentationExample_Representation_Index extends Asar_Representation {
  
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
    $template = new Asar_Template;
    $template->setTemplateFile(
      dirname(__FILE__) . DIRECTORY_SEPARATOR . 'index_xml_template.php'
    );
    return $template->render($data);
  }
  
}
