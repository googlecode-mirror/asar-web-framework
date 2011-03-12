<?php

class Asar_Resource 
  implements Asar_Resource_Interface, Asar_Config_Interface,
    Asar_PathDiscover_Interface
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
      'temporary' => 307,
    );
  
  function __construct() {
    $this->config_bag = new Asar_Config();
    $this->setUp();
    $this->config_bag->importConfig(new Asar_Config($this->config));
  }
  
  protected function setUp() {}
  
  function getConfig($key = null) {
    return $this->config_bag->getConfig($key);
  }
  
  protected function setConfig($key, $value) {
    $new_config_bag = new Asar_Config(array($key => $value));
    $new_config_bag->importConfig($this->config_bag);
    $this->config_bag = $new_config_bag;
  }
  
  function importConfig(Asar_Config_Interface $config) {
    return $this->config_bag->importConfig($config);
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $this->request = $request;
    $response = new Asar_Response(array(
      'headers' => array(
        'Content-Type' => $this->getConfig('default_content_type'),
        'Content-Language' => $this->getConfig('default_language')
      )
    ));
    
    try {
      if (!$this->qualify($this->getPathComponents())) {
        throw new Asar_Resource_Exception_NotFound;
      }
      $response->setContent(
        $this->runIfExists($request->getMethod())
      );
    } catch (Asar_Resource_Exception_NotFound $e) {
      $response->setStatus(404);
    } catch (Asar_Resource_Exception_MethodUndefined $e) {
      $response->setStatus(405);
      $response->setHeader('Allow', $this->getDefinedMethods());
    } catch (Asar_Resource_Exception_Redirect $e) {
      $payload = $e->getPayload();
      $response->setStatus($payload['status_code']);
      $response->setHeader('Location', $payload['location']);
      if (isset($payload['locations_list'])) {
        $response->setHeader(
          'Asar-Internal-Locationslist', $payload['locations_list']
        );
      }
    } catch (Asar_Resource_Exception_ForwardRequest $e) {
      throw $e;
    } catch (Exception $e) {
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
    throw new Asar_Resource_Exception_MethodUndefined;
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
    $e = new Asar_Resource_Exception_ForwardRequest($resource_name);
    $e->setPayload(array('request' => $this->request));
    throw $e;
  }
  
  function redirectTo($location, $type = 'basic') {
    if (is_array($location)) {
      $location_list = $location;
      $location = $location[0];
    }
    $e = new Asar_Resource_Exception_Redirect($location);
    $code = isset(self::$redirect_codes[$type]) ? 
      self::$redirect_codes[$type] : self::$redirect_codes['basic'];
    $e->setPayload(array('location' => $location, 'status_code' => $code));
    if (isset($location_list)) {
      $e->setPayload(array('locations_list' => $location_list));
    }
    throw $e;
  }
  
  function qualify($path) {
    return TRUE;
  }
  
  private function getResourceNameAsArray() {
    $cname = get_class($this);
    return explode('_', substr($cname, strpos($cname, '_Resource_') + 10));
  }
  
  function getPath() {
    return $this->request->getPath();
  }
  
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
    return '/' . implode(
      '/', array_map(array('Asar_Utility_String', 'dashLowerCase'), $touse)
    );
  }
  
  protected function getPathComponents() {
    $path_template = $this->getPathTemplate();
    $path = explode('/', $this->getPath());
    array_shift($path);
    $components2 = array();
    $i = 0;
    foreach ($path_template as $key => $value) {
      if (is_null($value)) {
        $components2[$key] = $path[$i];
      } else {
        $components2[$key] = $value;
      }
      $i++;
    }
    return $components2;
  }
  
  private function getPathTemplate() {
    if (!$this->path_template) {
      $this->path_template = array();
      $relevant = $this->getResourceNameAsArray();
      $keys = array_map(
        array('Asar_Utility_String', 'dashLowerCase'), $relevant
      );
      for ($i = 0, $size = count($keys); $i < $size; $i++) {
        if ($this->strStartsWith($keys[$i], 'rt-')) {
          $this->path_template[substr($keys[$i], 3)] = null;
        } else {
          $this->path_template[$keys[$i]] = $keys[$i];
        }
      }
    }
    return $this->path_template;
  }
  
  private function strStartsWith($str, $prefix) {
    return strpos($str, $prefix) === 0;
  }
  
}
