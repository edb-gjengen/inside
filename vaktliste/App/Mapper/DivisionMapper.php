<?php

/**
 * Division object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_DivisionMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM din_group WHERE id=?");
    $this->findByGroupIdStmt = self::$PDO->prepare(
      "SELECT * FROM din_division INNER JOIN din_group ON din_division.id = din_group.division_id WHERE din_group.id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM din_division");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_division (
        name,
        nicename,
        hidden
        ) values (?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_division SET
        name=?,
        nicename=?,
        hidden=?
        WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_division WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_Division($array["id"]);
    
    $obj->setName($array["name"]);
    $obj->setNiceName($array["nicename"]);
    $obj->setHidden($array["hidden"]);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getName());
    $this->insertStmt->bindValue(2, $object->getNiceName());
    $this->insertStmt->bindValue(3, $object->getHidden());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getName());
    $this->insertStmt->bindValue(2, $object->getNiceName());
    $this->insertStmt->bindValue(3, $object->getHidden());
    $this->updateStmt->bindValue(4, $object->getId());
    
    $this->updateStmt->execute();
  }

  protected function doDelete(App_Domain_DomainObject $object) {
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

  function getCollection(array $raw) {
    return new App_Mapper_DivsionCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_Division";
  }
}

?>