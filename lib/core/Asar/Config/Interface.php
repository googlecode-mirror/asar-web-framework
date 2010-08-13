<?php

interface Asar_Config_Interface {
  function getConfig($key = null);
  function importConfig(Asar_Config_Interface $config);
}