<?php

/**
 * Domain Membership model class
 *
 * @version 0.1
 */


class domain_Membership extends domain_DomainObject {
  private $user;
  private $card;
  private $created;
  private $starts;
  private $expires;

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

  function setCard(domain_MembershipCard $card) {
    $this->card = $card;
    $this->markDirty();
  }

  function getCard() {
    return $this->card;
  }

  function setCreated(DateTime $created) {
    $this->created = $created;
    $this->markDirty();
  }

  function getCreated() {
    return $this->created;
  }

  function setStarts(DateTime $starts) {
    $this->starts = $starts;
    $this->markDirty();
  }

  function getStarts() {
    return $this->starts;
  }

  function setExpires($expires) {
    $this->expires = $expires;
    $this->markDirty();
  }

  function getExpires() {
    return $this->expires;
  }

}

?>