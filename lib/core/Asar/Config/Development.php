<?php

class Asar_Config_Development extends Asar_Config {
  
  protected $config = array(
    'request_filters'          => array(
      'standard' => 'Asar_MessageFilter_Standard',
      'development' => 'Asar_MessageFilter_Development'
    ),
    'response_filters'          => array(
      'development' => 'Asar_MessageFilter_Development',
      'standard'    =>    'Asar_MessageFilter_Standard',
    ),
  );
  
}
