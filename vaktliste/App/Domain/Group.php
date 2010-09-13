<?php

/**
 * Domain Group model class
 *
 * @version 0.1
 */

class App_Domain_Group extends App_Domain_DomainObject {
  private $name;
  private $text;
  private $division;
  private $admin;
  private $mailinglist;
  
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
  
  function setText($text) {
    $this->text = $text;
    $this->markDirty();
  }
  
  function getText() {
    return $this->text;
  }
  
  function setDivision($division) {
    // check if division is an instance of App_Domain_Division
    if (!is_null($division)) {
      if (!($division instanceof App_Domain_Division)) {
        throw new Exception("Division must be an instance of division class or null");
      }
    }
    $this->division = $division;
    $this->markDirty();
  }
  
  function getDivision() {
    return $this->division;
  }
  
  function setAdmin($admin) {
    $this->admin = $admin;
    $this->markDirty();
  }
  
  function getAdmin() {
    return $this->admin;
  }
  
  function setMailinglist($mailinglist) {
    $this->mailinglist = $mailinglist;
    $this->markDirty();
  }
  
  function getMailinglist() {
    return $this->mailinglist;
  }
  
}

?>
