<?php
interface Asar_Message_Interface {
  function setHeader($name, $value);
  function getHeader($name);
  function setHeaders(array $headers);
  function getHeaders();
  function setContent($content);
  function getContent();
}