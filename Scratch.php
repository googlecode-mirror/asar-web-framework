<?php

/* A sample interface for Asar_View */

$config = array(
    'default_template_engine' => 'Asar_Template_Interface',
    'default_template_path'   => 'Representation',
    
);


var_dump($config);

class Churva {
    private $config;
    
    function setConfig(&$conf) {
        $this->config =& $conf;
    }
    
    function getConfig() {
        return $this->config;
    }
}

$chuva = new Churva;

$chuva->setConfig($config);

echo "\n\n************************\n\n";

$config['nanana'] = 'lalalala';

var_dump($chuva->getConfig());
