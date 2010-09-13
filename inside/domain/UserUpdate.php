<?php

/**
 * Domain UserUpdate model class
 *
 * @version 0.1
 */


class domain_UserUpdate extends domain_DomainObject {
  private $date;
  private $message;
  private $user;
  private $updateBy;

  function __construct($id = null) {
    parent::__construct($id);
  }

  function setDate($date_d) {
    $this->date = $date_d;
    $this->markDirty();
  }

  function getDate() {
    return $this->date;
  }

  function setMessage($message_s) {
    $this->message = $message_s;
    $this->markDirty();
  }

  function getMessage() {
    return $this->message;
  }

  function setUser($user) {
    $this->user = $user;
    $this->markDirty();
  }

  function getUser() {
    return $this->user;
  }

  function setUpdatedBy($updatedBy) {
    $this->updatedBy = $updatedBy;
    $this->markDirty();
  }

  function getUpdatedBy() {
    return $this->updatedBy;
  }

}

?>