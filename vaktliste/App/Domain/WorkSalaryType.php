<?php

/**
 * Domain WorkSalaryType model class
 *
 * @version 0.1
 */

class App_Domain_WorkSalaryType extends App_Domain_DomainObject {
  private $title;
  private $hourlyRate;
  
  function __construct($id = null) {
    parent::__construct($id);
  }
  
  function setTitle($title) {
    $this->title = $title;
    $this->markDirty();
  }
  
  function getTitle() {
    return $this->title;
  }
  
  function setHourlyRate($hourlyRate) {
    $this->hourlyRate = $hourlyRate;
    $this->markDirty();
  }
  
  function getHourlyRate() {
    return $this->hourlyRate;
  }
  
}

?>
