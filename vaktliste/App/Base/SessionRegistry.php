<?php

/**
 * Session registry
 *
 */
class App_Base_SessionRegistry extends App_Base_Registry {
  private static $instance;
  
  private function __construct() {
    session_start();
  }
  
  static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  protected function get($key) {
    if (isset($_SESSION[__CLASS__][$key])) {
      return $_SESSION[__CLASS__][$key];
    }
    return null;
  }
  
  protected function set($key, $val) {
    $_SESSION[__CLASS__][$key] = $val;
  }
  
  static function getUserId() {
    if (!is_null(self::instance()->get('userid'))) {
      //return self::instance()->get('userid');
    }
    // check session from old inside
    if (isset($_SESSION['valid-user'])) {
      self::instance()->set('userid', $_SESSION['valid-user']);
    }
    return self::instance()->get('userid');
  }
  
  static function setUserId($userid) {
    if (is_null($userid)) {
      unset($_SESSION['valid-user']);
      self::instance()->set('userid', null);
    } else {
      // update old inside as well
      $_SESSION['valid-user'] = $userid;
      self::instance()->set('userid', $userid);
    }
  }
  
}

?>