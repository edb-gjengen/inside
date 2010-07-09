<?php

/**
 * Location object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_LocationMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM lokaler WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM lokaler");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO lokaler (
        navn
        ) values (?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE lokaler SET
        navn=?
        WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM lokaler WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_Location($array["id"]);
    
    $obj->setName($array["navn"]);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getName());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getName());
    $this->updateStmt->bindValue(2, $object->getId());
    
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
    return new App_Mapper_LocationCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_Location";
  }
}

?>