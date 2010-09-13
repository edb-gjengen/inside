<?php

/**
 * Domain WorkShiftUpdate model class
 *
 * @version 0.1
 */

class App_Domain_WorkShiftUpdate extends App_Domain_DomainObject {
  private $shift;
  private $updateTime;
  private $user;
  private $updateText;
  
  function __construct($id = null) {
    parent::__construct($id);
  }
  
  function setShift(App_Domain_WorkShift $shift) {
    $this->shift = $shift;
    $this->markDirty();
  }
  
  function getShift() {
    return $this->shift;
  }
  
  function setUpdateTime(DateTime $time) {
    $this->updateTime = $time;
    $this->markDirty();
  }
  
  function getUpdateTime() {
    return $this->updateTime;
  }
  
  function setUser(App_Domain_User $user) {
    $this->user = $user;
    $this->markDirty();
  }
  
  function getUser() {
    return $this->user;
  }
  
  function setUpdateText($updateText) {
    $this->updateText = $updateText;
    $this->markDirty();
  }
  
  function getUpdateText() {
    return $this->updateText;
  }
  
}

?>
