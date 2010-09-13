<?php

/**
 * User address object mapper to database
 *
 * @version 0.1
 */

class mapper_UserAddressMapper extends mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT user_id, street, zipcode FROM din_useraddressno WHERE user_id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT user_id, street, zipcode FROM din_useraddressno");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_useraddressno SET
        street=?,
        zipcode=?,
        WHERE user_id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_useraddressno (
        user_id, street, zipcode
        ) values (?, ?, ?)");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_useraddressno WHERE user_id=?");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT user_id, street, zipcode FROM din_useraddressno WHERE user_id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new domain_Membership($array["user_id"]);
    
    $user_mapper = new mapper_UserMapper();
    $obj->setUser($user_mapper->find($array["user_id"]));
    
    $obj->setStreet($array["street"]);
    $obj->setZipCode($array["zipcode"]);
    return $obj;
  }

  protected function doInsert(domain_DomainObject $object) {
    $values = array($object->getUser()->getId(), $object->getStreet(), $object->getZipCode());
    $this->insertStmt->execute($values);
    $id = self::$PDO->lastInsertId();
    $object->setId($id);
  }

  function update(domain_DomainObject $object) {
    $values = array(
      $object->getStreet(),
      $object->getZipCode(),
      $object->getId()
      );
    $this->updateStmt->execute($values);
  }

  protected function doDelete(domain_DomainObject $object) {
    $values = array($object->getId());
    $this->deleteStmt->execute($values);
  }

  function selectStmt() {
    return $this->selectStmt;
  }

  function selectAllStmt() {
    return $this->selectAllStmt;
  }

  function deleteStmt() {
    return $this->deleteStmt;
  }

  function findByUserId($userid) {
    $this->findByUserIdStmt->execute(array($userid));
    return new mapper_UserAddressCollection($this->findByUserIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new mapper_UserAddressCollection($raw, $this);
  }

  protected function targetClass() {
    return "domain_UserAddress";
  }
}

?>