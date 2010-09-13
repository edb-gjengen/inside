<?php

/**
 * Domain Location model class
 *
 * @version 0.1
 */

class App_Domain_Location extends App_Domain_DomainObject {
  private $name;
  
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
  
}

?>
