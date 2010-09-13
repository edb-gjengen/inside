<?php

/**
 * Retrieves HTTP requests
 *
 */
class App_Controller_Request {
  private $properties;
  private $feedback = array ();
  private $command = null;
  
  function __construct() {
    $this->init();
    App_Base_RequestRegistry::setRequest($this);
  }
  
  function init() {
    if (isset($_SERVER['REQUEST_METHOD'])) {
      if ($_SERVER['REQUEST_METHOD']) {
        $this->properties = $_REQUEST;
        return;
      }
    }
    
    foreach($_SERVER['argv'] as $arg) {
      if (strpos($arg, '=')) {
        list($key, $val) = explode("=", $arg);
        $this->setProperty($key, $val);
      }
    }
  }
  
  function getProperty($key) {
    if (isset ($this->properties[$key])) {
      return $this->properties[$key];
    }
    return null;
  }
  
  function setProperty($key, $val) {
    $this->properties[$key] = $val;
  }
  
  function addFeedback($msg) {
    array_push($this->feedback, $msg);
  }
  
  function getFeedback() {
    return $this->feedback;
  }
  
  function getFeedbackString($separator="\n") {
    return implode($separator, $this->feedback);
  }

  function setObject($name, $object) {
      $this->objects[$name] = $object;
  }

  function getObject($name) {
    if ( isset( $this->objects[$name] ) ) {
      return $this->objects[$name];
    }
    return null;
  }
  
  function setCommand (App_Command_Command $cmd) {
    return $this->command = $cmd;
  }
  
  function getLastCommand () {
    return $this->command;
  }
}
?>