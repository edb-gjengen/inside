<?php

class MembershipCard {
  protected static $PDO;
  const DATETIME_FORMAT = "Y-m-d H:i:s";
  
  private $id;
  private $userId;
  private $ordered;
  private $produced;
  private $delivered;
  private $active;
  
  private $new;
  private $updated;
  private $delete;

  function __construct() {
    // connect to DB
    if (!isset(self::$PDO)) {
      $dsn = getDSN("PDO");
      if (is_null($dsn)) {
        throw new Exception("No DSN");
      }
      try {
        self::$PDO = new PDO($dsn["dsn"], $dsn["username"], $dsn["password"]);
        self::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (Exception $e) {
        throw new Exception("Database connection error: ". getDSN());
      }
    }
    
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM din_membershipcard WHERE id=?");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT * FROM din_membershipcard WHERE userId=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_membershipcard (userId, ordered, produced, delivered, active) VALUES (?, ?, ?, ?, ?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_membershipcard SET 
      	userId=?,
      	ordered=?,
      	produced=?,
      	delivered=?,
      	active=?
      	WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_membershipcard WHERE id=?");
    
    $this->active = 0;
    
    $this->new = true;
    $this->updated = false;
    $this->delete = false;
  }
  
  function findByUserId($userId) {
    $this->findByUserIdStmt->execute(array($userId));
    
    $array = $this->findByUserIdStmt->fetch();
    $this->findByUserIdStmt->closeCursor();
    if (!is_array($array)) {
      return false;
    }
    if (!isset($array['id'])) {
      return false;
    }
    
    $this->setId($array['id']);
    $this->setUserId($array['userId']);
    if (!is_null($array['ordered'])) {
      $this->setOrdered(new DateTime($array['ordered']));
    }
    if (!is_null($array['produced'])) {
      $this->setProduced(new DateTime($array['produced']));
    }
    if (!is_null($array['delivered'])) {
      $this->setDelivered(new DateTime($array['delivered']));
    }
    $this->setActive($array['active']);
    
    $this->updated = false;
    $this->new = false;
    return true;
  }
  
  private function doInsert() {
    $this->insertStmt->bindValue(1, $this->getUserId());
    
    if (!is_null($this->getOrdered())) {
      $this->insertStmt->bindValue(2, $this->getOrdered()->format(self::DATETIME_FORMAT));
    } else {
      $this->insertStmt->bindValue(2, null);
    }
    
    if (!is_null($this->getProduced())) {
      $this->insertStmt->bindValue(3, $this->getProduced()->format(self::DATETIME_FORMAT));
    } else {
      $this->insertStmt->bindValue(3, null);
    }
    
    if (!is_null($this->getDelivered())) {
      $this->insertStmt->bindValue(4, $this->getDelivered()->format(self::DATETIME_FORMAT));
    } else {
      $this->insertStmt->bindValue(4, null);
    }
    
    $this->insertStmt->bindValue(5, $this->getActive());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
    
    $this->updated = false;
    $this->new = false;
  }
  
  private function doUpdate() {
    $this->updateStmt->bindValue(1, $this->getUserId());
    
    if (!is_null($this->getOrdered())) {
      $this->updateStmt->bindValue(2, $this->getOrdered()->format(self::DATETIME_FORMAT));
    } else {
      $this->updateStmt->bindValue(2, null);
    }
    
    if (!is_null($this->getProduced())) {
      $this->updateStmt->bindValue(3, $this->getProduced()->format(self::DATETIME_FORMAT));
    } else {
      $this->updateStmt->bindValue(3, null);
    }
    
    if (!is_null($this->getDelivered())) {
      $this->updateStmt->bindValue(4, $this->getDelivered()->format(self::DATETIME_FORMAT));
    } else {
      $this->updateStmt->bindValue(4, null);
    }
    
    $this->updateStmt->bindValue(5, $this->getActive());
    
    $this->updateStmt->bindValue(6, $this->getId());
    
    $this->updateStmt->execute();
    
    $this->updated = false;
    $this->new = false;
  }
  
  private function doDelete() {
    $values = array($this->getId());
    $this->deleteStmt->execute($values);
  }
  
  function store() {
    if ($this->delete) {
      $this->doDelete();
    } elseif ($this->new && $this->updated) {
      $this->doInsert();
    } elseif ($this->updated) {
      $this->doUpdate();
    }
  }
  
  function getId() {
    return $this->id;
  }
  
  function setId($id) {
    $this->id = $id;
    $this->updated = true;
  }
  
  function getUserId() {
    return $this->userId;
  }
  
  function setUserId($userId) {
    $this->userId = $userId;
    $this->updated = true;
  }
  
  function getOrdered() {
    return $this->ordered;
  }
  
  function setOrdered(DateTime $ordered) {
    $this->ordered = $ordered;
    $this->updated = true;
  }
  
  function getProduced() {
    return $this->produced;
  }
  
  function setProduced(DateTime $produced) {
    $this->produced = $produced;
    $this->updated = true;
  }
  
  function getDelivered() {
    return $this->delivered;
  }
  
  function setDelivered(DateTime $delivered) {
    $this->delivered = $delivered;
    $this->updated = true;
  }
  
  function getActive() {
    return $this->active;
  }
  
  function setActive($active) {
    $this->active = $active;
    $this->updated = true;
  }
}

?>