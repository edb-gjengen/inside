<?php

class App_Base_ApplicationRegistry extends App_Base_Registry {
  private static $instance;
  //private $freezedir = "data";
  private $values = array();
  //private $mtimes = array();
  
  private function __construct() {}
  
  static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  protected function get($key) {
    // more get logic here ...
    if (isset($this->values[$key])) {
      return $this->values[$key];
    }
    return null;
  }
  
  protected function set($key, $val) {
    $this->values[$key] = $val;
    // storage ...
    return $this->values[$key];
  }
  
  static function getDSN() {
    return self::instance()->get('dsn');
  }
  
  
  static function setDSN( $dsn ) {
    return self::instance()->set('dsn', $dsn);
  }
  
  static function getControllerMap() {
    return self::instance()->get('controllerMap');
  }
  
  static function setControllerMap(App_Controller_ControllerMap $map ) {
    return self::instance()->set('controllerMap', $map);
  }
  
  static function getApplicationController() {
    return self::instance()->get('applicationController');
  }
  
  static function setApplicationController(App_Controller_AppController $appCtrl) {
    return self::instance()->set('applicationController', $appCtrl);
  }
  
  static function getAccessManager() {
    return self::instance()->get('accessManager');
  }
  
  static function setAccessManager(App_Controller_AccessManager $manager) {
    return self::instance()->set('accessManager', $manager);
  }
  
  static function getPageTitle() {
    return self::instance()->get('pagetitle');
  }
  
  static function setPageTitle( $pagetitle ) {
    return self::instance()->set('pagetitle', $pagetitle);
  }
  
}