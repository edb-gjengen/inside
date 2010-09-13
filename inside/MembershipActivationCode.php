<?php

class MembershipActivationCode {
  protected static $PDO;
  const DATETIME_FORMAT = "Y-m-d H:i:s";
  
  private $id;
  private $userId;
  private $code;
  private $ordered;
  private $used;
  
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
      "SELECT * FROM din_membership_activationcode WHERE id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_membership_activationcode (userId, ordered, used) VALUES (?, ?, ?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_membership_activationcode SET 
      	userId=?,
      	ordered=?,
      	used=?
      	WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_membership_activationcode WHERE id=?");
    
    $this->new = true;
    $this->updated = false;
    $this->delete = false;
  }
  
  function findById($id) {
    $this->selectStmt->execute(array($id));
    
    $array = $this->findByIdStmt->fetch();
    $this->findByIdStmt->closeCursor();
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
    if (!is_null($array['used'])) {
      $this->setUsed(new DateTime($array['used']));
    }
  
    // generate code from id
    $this->setCode(substr(crypt(trim($this->getId()), 1813), 2, 6));
	
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
    
    if (!is_null($this->getUsed())) {
      $this->insertStmt->bindValue(3, $this->getUsed()->format(self::DATETIME_FORMAT));
    } else {
      $this->insertStmt->bindValue(3, null);
    }
    
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
    
    if (!is_null($this->getUsed())) {
      $this->updateStmt->bindValue(3, $this->getUsed()->format(self::DATETIME_FORMAT));
    } else {
      $this->updateStmt->bindValue(3, null);
    }
    
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
  
  function getCode() {
    return $this->code;
  }
  
  function setCode($code) {
    $this->code = $code;
    $this->updated = true;
  }
  
  function getOrdered() {
    return $this->ordered;
  }
  
  function setOrdered(DateTime $ordered) {
    $this->ordered = $ordered;
    $this->updated = true;
  }
  
  function getUsed() {
    return $this->used;
  }
  
  function setUsed(DateTime $used) {
    $this->used = $used;
    $this->updated = true;
  }
}

?>