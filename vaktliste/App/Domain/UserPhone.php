<?php

/**
 * Domain UserPhone model class
 *
 * @version 0.1
 */


class App_Domain_UserPhone extends App_Domain_DomainObject {
  private $user;
  private $phone;

  function __construct($id = null) {
    parent::__construct($id);
  }

  function setUser(domain_User $user) {
    $this->user = $user;
    $this->markDirty();
  }

  function getUser() {
    return $this->user;
  }

  function setPhone($phone) {
    $this->phone = $phone;
    $this->markDirty();
  }

  function getPhone() {
    return $this->phone;
  }

}

?>