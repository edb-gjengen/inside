<?php

/**
 * Domain Object parent class
 *
 * @version 0.1
 */


abstract class App_Domain_DomainObject {
  private $id = -1;

  function __construct($id = null) {
    if (is_null($id)) {
      $this->markNew();
    } else {
      $this->id = $id;
    }
  }

  function markNew() {
    App_Domain_ObjectWatcher::addNew($this);
  }

  function markDeleted() {
    App_Domain_ObjectWatcher::addDelete($this);
  }

  function markDirty() {
    App_Domain_ObjectWatcher::addDirty($this);
  }

  function markClean() {
    App_Domain_ObjectWatcher::addClean($this);
  }

  function setId($id) {
    $this->id = $id;
  }

  function getId() {
    return $this->id;
  }

  function collection() {
    return self::getCollection(get_class($this));
  }

  static function getCollection($type) {
    return App_Domain_HelperFactory::getCollection($type);
  }

  function finder() {
    return self::getFinder(get_class($this));
  }

  static function getFinder($type) {
    return App_Domain_HelperFactory::getFinder($type);
  }

}

?>