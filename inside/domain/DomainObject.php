<?php

/**
 * Domain Object parent class
 *
 * @version 0.1
 */


abstract class domain_DomainObject {
  private $id = -1;

  function __construct($id = null) {
    if (is_null($id)) {
      $this->markNew();
    } else {
      $this->id = $id;
    }
  }

  // Unit of Work pattern - not implemented yet
  function markNew() {
    domain_ObjectWatcher::addNew($this);
  }

  function markDeleted() {
    domain_ObjectWatcher::addDelete($this);
  }

  function markDirty() {
    domain_ObjectWatcher::addDirty($this);
  }

  function markClean() {
    domain_ObjectWatcher::addClean($this);
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
    return domain_HelperFactory::getCollection($type);
  }

  function finder() {
    return self::getFinder(get_class($this));
  }

  static function getFinder($type) {
    return domain_HelperFactory::getFinder($type);
  }

}

?>