<?php

/**
 * Domain UserAddress model class
 *
 * @version 0.1
 */


class domain_UserAddress extends domain_DomainObject {
  private $user;
  private $phone;
  private $address;

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

  function setAddress($address) {
    $this->phone = $address;
    $this->markDirty();
  }

  function getAddress() {
    return $this->address;
  }

}

?>