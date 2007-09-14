<?php
interface Asar_Requestable {
  function processRequest(Asar_Request $request, array $arguments = NULL);
}
?>
