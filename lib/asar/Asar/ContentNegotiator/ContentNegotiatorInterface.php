<?php
namespace Asar\ContentNegotiator;
/**
 * Provides an interface for content negotiation like how HTTP servers negotiate
 * the Content-type and/or language through an HTTP Request accept header.
 *
 * @package Asar
 * @subpackage core
 */
interface ContentNegotiatorInterface {
  
  /**
   * @param string $accept_header the accept header passed by a request which
   *                              can as simple as 'text/plain' or as
   *                              complicated as 'text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8'     
   * @param array $available_formats an array listing the available formats
   *                                 supported by the framework or application
   * @return string|boolean returns the preferred format or false if there is
   *                        no match
   */
  function negotiateFormat($accept_header, array $available_formats);
}
