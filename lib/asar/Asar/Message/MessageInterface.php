<?php
namespace Asar\Message;
/**
 * A generic message interface that Asar_Request and Asar_Response shares.
 *
 */
interface MessageInterface {
  function setHeader($name, $value);
  function getHeader($name);
  function setHeaders(array $headers);
  function getHeaders();
  function setContent($content);
  function getContent();
}
