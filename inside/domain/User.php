<?php

/**
 * Domain User Model class
 *
 * @version 0.1
 */


class domain_User extends domain_DomainObject {
  private $username;
  private $firstname;
  private $lastname;
  private $password;
  private $validaddress;
  private $email;
  private $birthdate;
  private $placeOfStudy;
  private $passwordreset;
  private $divisionIdRequest;
  private $updates;
  private $memberships;
  private $address;
  private $cards;


  function __construct($id = null) {
    parent::__construct($id);
  }

  function setUsername($username_s) {
    $this->username = $username_s;
    $this->markDirty();
  }

  function getUsername() {
    return $this->username;
  }

  function setPassword($password_s) {
    $this->password = $password_s;
    $this->markDirty();
  }

  function getPassword() {
    return $this->password;
  }

  function setFirstname($firstname_s) {
    $this->firstname = $firstname_s;
    $this->markDirty();
  }

  function getFirstname() {
    return $this->firstname;
  }

  function setLastname($lastname_s) {
    $this->lastname = $lastname_s;
    $this->markDirty();
  }

  function getLastname() {
    return $this->lastname;
  }

  function setValidaddress($validaddress_i) {
    $this->validaddress = $validaddress_i;
    $this->markDirty();
  }

  function getValidaddress() {
    return $this->validaddress;
  }

  function setEmail($email_s) {
    $this->email = $email_s;
    $this->markDirty();
  }

  function getEmail() {
    return $this->email;
  }

  function setBirthdate($birthdate_d) {
    $this->birthdate = $birthdate_d;
    $this->markDirty();
  }

  function getBirthdate() {
    return $this->birthdate;
  }

  function setPlaceOfStudy($placeofstudy_i) {
    $this->placeOfStudy = $placeofstudy_i;
    $this->markDirty();
  }

  function getPlaceOfStudy() {
    return $this->placeOfStudy;
  }

  function setPasswordreset($passwordreset_i) {
    $this->passwordreset = $passwordreset_i;
    $this->markDirty();
  }

  function getPasswordreset() {
    return $this->passwordreset;
  }

  function setDivisionIdRequest($divisionidrequest_i) {
    $this->divisionIdRequest = $divisionidrequest_i;
    $this->markDirty();
  }

  function getDivisionIdRequest() {
    return $this->divisionIdRequest;
  }

  function getUpdates() {
    if (is_null($this->updates)) {
      $this->updates = self::getFinder("domain_UserUpdate")->findByUserId($this->getId());
    }
    return $this->updates;
  }

  function getAddress() {
    if (is_null($this->address)) {
      $this->address = self::getFinder("domain_UserAddress")->findByUserId($this->getId());
    }
    return $this->address;
  }

  function getMemberships() {
    if (is_null($this->memberships)) {
      $this->memberships = self::getFinder("domain_Membership")->findByUserId($this->getId());
    }
    return $this->memberships;
  }

  function getCards() {
    if (is_null($this->cards)) {
      $this->cards = self::getFinder("domain_MembershipCard")->findByUserId($this->getId());
    }
    return $this->cards;
  }
}

?>