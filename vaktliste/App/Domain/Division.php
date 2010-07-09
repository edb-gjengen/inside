<?php

/**
 * Domain Division model class
 *
 * @version 0.1
 */

class App_Domain_Division extends App_Domain_DomainObject {
  private $name;
  private $nicename;
  private $hidden;
  
  function __construct($id = null) {
    parent::__construct($id);
  }
  
  function setName($name) {
    $this->name = $name;
    $this->markDirty();
  }
  
  function getName() {
    return $this->name;
  }
  
  function setNiceName($niceName) {
    $this->niceName = $niceName;
    $this->markDirty();
  }
  
  function getNiceName() {
    return $this->niceName;
  }
  
  function setHidden($hidden) {
    $this->hidden = $hidden;
    $this->markDirty();
  }
  
  function getHidden() {
    return $this->hidden;
  }
  
}

?>
