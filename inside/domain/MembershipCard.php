<?php

/**
 * Domain MembershipCard model class
 *
 * @version 0.1
 */


class domain_MembershipCard extends domain_DomainObject {
  private $user;
  private $created;
  private $produced;
  private $lastSticker;
  private $stickers;
  private $memberships;

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

  function setCreated(DateTime $created) {
    $this->created = $created;
    $this->markDirty();
  }

  function getCreated() {
    return $this->created;
  }

  function setProduced(DateTime $produced) {
    $this->produced = $produced;
    $this->markDirty();
  }

  function getProduced() {
    return $this->produced;
  }
  
  function setLastSticker($laststicker) {
    $this->lastSticker = $laststicker;
    $this->markDirty();
  }
  
  function getLastSticker() {
    return $this->lastSticker;
  }
    
  function getStickers() {
//    if (is_null($this->stickers)) {
//      $this->stickers = self::getFinder("domain_MembershipCardSticker")->findByCardId($this->getId());
//    }
//    return $this->stickers;
    return null;
  }

  function getMemberships() {
    if (is_null($this->memberships)) {
      $this->memberships = self::getFinder("domain_Membership")->findByCardId($this->getId());
    }
    return $this->memberships;
  }

}

?>
