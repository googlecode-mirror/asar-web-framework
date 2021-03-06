<?php

namespace Asar;

use \Asar\Resource\ResourceInterface;
use \Asar\Resource\Exception\NotFound;
use \Asar\Resource\Exception\MethodUndefined;
use \Asar\Resource\Exception\ForwardRequest;
use \Asar\Resource\Exception\Redirect;
use \Asar\Config\ConfigInterface;
use \Asar\Config;
use \Asar\PathDiscover\PathDiscoverInterface;
use \Asar\Response;
use \Asar\Request\RequestInterface;
use \Asar\Utility\String;

/**
 */
class Resource 
  implements ResourceInterface, ConfigInterface, PathDiscoverInterface
{
  protected
    $request,
    $config_bag,
    $path_template,
    $config = array();
  
  protected static
    $redirect_codes = array(
      'multiple'  => 300,
      'permanent' => 301,
      'basic'     => 302,
      'see'       => 303,
      'temporary' => 307,
    );
  
  function __construct() {
    $this->config_bag = new Config();
    $this->setUp();
    $this->config_bag->importConfig(new Config($this->config));
  }
  
  protected function setUp() {}
  
  function getConfig($key = null) {
    return $this->config_bag->getConfig($key);
  }
  
  protected function setConfig($key, $value) {
    $new_config_bag = new Config(array($key => $value));
    $new_config_bag->importConfig($this->config_bag);
    $this->config_bag = $new_config_bag;
  }
  
  function importConfig(ConfigInterface $config) {
    return $this->config_bag->importConfig($config);
  }
  
  function handleRequest(RequestInterface $request) {
    $this->request = $request;
    $response = new Response(array(
      'headers' => array(
        'Content-Type' => $this->getConfig('default_content_type'),
        'Content-Language' => $this->getConfig('default_language')
      )
    ));
    
    try {
      if (!$this->qualify($this->getPathComponents())) {
        throw new NotFound;
      }
      $response->setContent(
        $this->runIfExists($request->getMethod())
      );
    } catch (NotFound $e) {
      $response->setStatus(404);
    } catch (MethodUndefined $e) {
      $response->setStatus(405);
      $response->setHeader('Allow', $this->getDefinedMethods());
    } catch (Redirect $e) {
      $payload = $e->getPayload();
      $response->setStatus($payload['status_code']);
      $response->setHeader('Location', $payload['location']);
      if (isset($payload['locations_list'])) {
        $response->setHeader(
          'Asar-Internal-Locationslist', $payload['locations_list']
        );
      }
    } catch (ForwardRequest $e) {
      throw $e;
    } catch (\Exception $e) {
      $response->setStatus(500);
      $response->setContent($e->getMessage());
    }
    return $response;
  }
  
  private function runIfExists($method) {
    if (method_exists($this, $method)) {
      if ($method == 'POST') {
        $post_content = $this->request->getContent();
        $_POST = $post_content ? $post_content : array();
      }
      return $this->$method();
    }
    throw new MethodUndefined;
  }
  
  private function getDefinedMethods() {
    $methods = array('GET', 'POST', 'PUT', 'DELETE');
    $allowed = array();
    foreach ($methods as $method) {
      if (method_exists($this, $method)) {
        $allowed[] = $method;
      }
    }
    return implode(', ', $allowed);
  }
  
  function forwardTo($resource_name) {
    $exception = new ForwardRequest($resource_name);
    $exception->setPayload(array('request' => $this->request));
    throw $exception;
  }
  
  function redirectTo($location, $type = 'basic') {
    if (is_array($location)) {
      $location_list = $location;
      $location = $location[0];
    }
    $exception = new Redirect($location);
    $code = isset(self::$redirect_codes[$type]) ? 
      self::$redirect_codes[$type] : self::$redirect_codes['basic'];
    $exception->setPayload(
      array('location' => $location, 'status_code' => $code)
    );
    if (isset($location_list)) {
      $exception->setPayload(array('locations_list' => $location_list));
    }
    throw $exception;
  }
  
  function qualify($path) {
    return TRUE;
  }
  
  /**
   * @note Resource class names should start with only one instance of the 
   * string '\Resource\' and that is after the application namespace.
   * So an application name that has '\Resource\' in it will throw an error.
   */
  private function getResourceNameAsArray() {
    $cname = get_class($this);
    return explode('\\', substr($cname, strpos($cname, '\Resource\\') + 10));
  }
  
  function getPath() {
    return $this->request->getPath();
  }
  
  /**
   * @todo This should probably delegate to the router...
   */
  function getPermaPath($path_params = array()) {
    $touse = array();
    $path_template = $this->getPathTemplate();
    foreach ($path_template as $key => $value) {
      if (is_null($value)) {
        $touse[] = $path_params[$key];
      } else {
        $touse[] = $value;
      }
    }
    $permapath = '/' . implode(
      '/', array_map(array('Asar\Utility\String', 'dashLowerCase'), $touse)
    );
    return $permapath == '/index' ? '/' : $permapath;
  }
  
  protected function getPathComponents() {
    $path_template = $this->getPathTemplate();
    $path = explode('/', $this->getPath());
    array_shift($path);
    $components2 = array();
    $count = 0;
    foreach ($path_template as $key => $value) {
      if (is_null($value)) {
        $components2[$key] = $path[$count];
      } else {
        $components2[$key] = $value;
      }
      $count++;
    }
    return $components2;
  }
  
  private function getPathTemplate() {
    if (!$this->path_template) {
      $this->path_template = array();
      $relevant = $this->getResourceNameAsArray();
      $keys = array_map(
        array('Asar\Utility\String', 'dashLowerCase'), $relevant
      );
      for ($i = 0, $size = count($keys); $i < $size; $i++) {
        if (String::startsWith($keys[$i], 'rt-')) {
          $this->path_template[substr($keys[$i], 3)] = null;
        } else {
          $this->path_template[$keys[$i]] = $keys[$i];
        }
      }
    }
    return $this->path_template;
  }
  
}
