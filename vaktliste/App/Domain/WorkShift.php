<?php

/**
 * Domain WorkShift model class
 *
 * @version 0.1
 */


class App_Domain_WorkShift extends App_Domain_DomainObject {
  private $starts;
  private $ends;
  private $group;
  private $location;
  private $active;
  private $title;
  private $comment;
  private $numWorkers;
  private $locked;
  private $autoassignWorkers;
  private $salaryType;
  private $workers;
  private $updates;
  
  function __construct($id = null) {
    parent::__construct($id);
  }
  
  function setStarts(DateTime $starts) {
    $this->starts = $starts;
    $this->markDirty();
  }
  
  function getStarts() {
    return $this->starts;
  }
  
  function setEnds(DateTime $ends) {
    $this->ends = $ends;
    $this->markDirty();
  }
  
  function getEnds() {
    return $this->ends;
  }
  
  function setGroup(App_Domain_Group $group) {
    $this->group = $group;
    $this->markDirty();
  }
  
  function getGroup() {
    return $this->group;
  }
  
  function setLocation(App_Domain_Location $location) {
    $this->location = $location;
    $this->markDirty();
  }
  
  function getLocation() {
    return $this->location;
  }
  
  function setActive($active) {
    $this->active = $active;
    $this->markDirty();
  }
  
  function getActive() {
    return $this->active;
  }
  
  function setTitle($title) {
    $this->title = $title;
    $this->markDirty();
  }
  
  function getTitle() {
    return $this->title;
  }
  
  function setComment($comment) {
    $this->comment = $comment;
    $this->markDirty();
  }
  
  function getComment() {
    return $this->comment;
  }
  
  function setNumWorkers($numWorkers) {
    $this->numWorkers = $numWorkers;
    $this->markDirty();
  }
  
  function getNumWorkers() {
    return $this->numWorkers;
  }
  
  function setLocked($locked) {
    $this->locked = $locked;
    $this->markDirty();
  }
  
  function getLocked() {
    return $this->locked;
  }
  
  function setAutoassignWorkers($autoassignWorkers) {
    $this->autoassignWorkers = $autoassignWorkers;
    $this->markDirty();
  }
  
  function getAutoassignWorkers() {
    return $this->autoassignWorkers;
  }
  
  function setSalaryType(App_Domain_WorkSalaryType $salaryType) {
    $this->salaryType = $salaryType;
    $this->markDirty(); 
  }
  
  function getSalaryType() {
    return $this->salaryType;
  }

  function getWorkers() {
    if (is_null($this->workers)) {
      $this->workers = self::getFinder("App_Domain_WorkShiftWorkers")->findByShiftId($this->getId());
    }
    return $this->workers;
  }

  function getUpdates() {
    if (is_null($this->updates)) {
      $this->updates = self::getFinder("App_Domain_WorkShiftUpdates")->findByShiftId($this->getId());
    }
    return $this->updates;
  }
  
}