<?php

/**
 * Domain WorkShiftWorker model class
 *
 * @version 0.1
 */

class App_Domain_WorkShiftWorker extends App_Domain_DomainObject {
  private $shift;
  private $user;
  private $assigned;
  private $noShow;
  private $userComment;
  private $employerComment;
  
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
  
  function setUser(App_Domain_User $user) {
    $this->user = $user;
    $this->markDirty();
  }
  
  function getUser() {
    return $this->user;
  }
  
  function setAssigned($assigned) {
    $this->assigned = $assigned;
    $this->markDirty();
  }
  
  function getAssigned () {
    return $this->assigned;
  }
  
  function setNoShow($noShow) {
    $this->noShow = $noShow;
    $this->markDirty();
  }
  
  function getNoShow() {
    return $this->noShow;
  }
  
  function setUserComment($comment) {
    $this->userComment = $comment;
    $this->markDirty();
  }
  
  function getUserComment() {
    return $this->userComment;
  }
  
  function setEmployerComment($comment) {
    $this->employerComment = $comment;
    $this->markDirty();
  }
  
  function getEmployerComment() {
    return $this->employerComment;
  }
}

?>
