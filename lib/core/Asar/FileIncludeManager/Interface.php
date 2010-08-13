<?php

interface Asar_FileIncludeManager_Interface {
  function requireFileOnce($file);
  function includeFile($file);
}