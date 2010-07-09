<?php

/**
 * Identity map for ensuring that one object doesnt become two
 *
 * @version 0.1
 */

class App_Domain_ObjectWatcher {
  private $all = array();
  private $dirty = array();
  private $new = array();
  private $delete = array(); // not implemented
  private static $instance;

  private function __construct() { }

  static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new App_Domain_ObjectWatcher();
    }
    return self::$instance;
  }

  function globalKey(App_Domain_DomainObject $obj) {
    $key = get_class($obj) . "." . $obj->getId();
    return $key;
  }

  static function add(App_Domain_DomainObject $obj) {
    $inst = self::instance();
    $inst->all[$inst->globalKey($obj)] = $obj;
  }

  static function exists($classname, $id) {
    $inst = self::instance();
    $key = "$classname.$id";
    if (isset($inst->all[$key])) {
      return $inst->all[$key];
    }
    return null;
  }

  static function addDirty(App_Domain_DomainObject $obj) {
    $inst = self::instance();
    // verify that the updated object is an object allready in the database
    if (!in_array($obj, $inst->new, true)) {
      $inst->dirty[$inst->globalKey($obj)] = $obj;
    }
  }

  static function addNew(App_Domain_DomainObject $obj) {
    $inst = self::instance();
    // we don't yet have an id
    $inst->new[] = $obj;
  }

  /*
   * Mark object as clean, changes will not be stored to database
   */
  static function addClean(App_Domain_DomainObject $obj) {
    $inst = self::instance();

    unset($inst->dirty[$inst->globalKey($obj)]);

    // check if the object is in the new array, and remove it from it
    if (in_array($obj, $inst->new, true)) {
      $pruned=array();
      foreach ($inst->new as $newobj) {
        if (!($newobj === $obj)) {
          $pruned[] = $newobj;
        }
      }
      $inst->new = $pruned;
    }
  }

  static function addDelete(App_Domain_DomainObject $obj) {
    $inst = self::instance();

    unset($inst->dirty[$inst->globalKey($obj)]);

    if (in_array($obj, $inst->new, true)) {
      $pruned=array();
      foreach ($inst->new as $newobj) {
        if (!($newobj === $obj)) {
          $pruned[] = $newobj;
        }
      }
      $inst->new = $pruned;
    }

    $inst->delete[] = $obj;
  }

  function performOperations() {
    foreach ($this->dirty as $key=>$obj) {
      $obj->finder()->update($obj);
    }
    foreach ($this->new as $key=>$obj) {
      $obj->finder()->insert($obj);
    }
    foreach ($this->delete as $key=>$obj) {
      //$obj->finder()->delete($obj);
    }
    $this->dirty = array();
    $this->new = array();
    $this->delete = array();
  }
}

?>