<?php
for($i=0, $s=""; $i<250; $s=socket_strerror($i), $i++)
  !empty($s) && ('Unknown error' != (substr($s,0,13)) ) && print "{$i} => {$s}\n";