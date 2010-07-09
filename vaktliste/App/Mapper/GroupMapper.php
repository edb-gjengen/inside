<?php

/**
 * Group object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_GroupMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM din_group WHERE id=?");
    $this->findByDivisionIdStmt = self::$PDO->prepare(
      "SELECT * FROM din_group WHERE division_id=?");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT * FROM din_group INNER JOIN din_usergrouprelationship ON din_group.id = din_usergrouprelationship.group_id WHERE din_usergrouprelationship.user_id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM din_group");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_group (
        name,
        text,
        division_id,
        admin,
        mailinglist
        ) values (?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_group SET
        name=?,
        text=?,
        division_id=?,
        admin=?,
        mailinglist=?
        WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_group WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_Group($array["id"]);
    
    $obj->setName($array["name"]);
    $obj->setText($array["text"]);
    $obj->setAdmin($array["admin"]);
    $obj->setMailinglist($array["mailinglist"]);
    
    // find division and create division object
    $division_mapper = new App_Domain_DivisionMapper();
    $division = $division_mapper->find($array["division_id"]);
    $obj->setDivision($division);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getName());
    $this->insertStmt->bindValue(2, $object->getText());
    $this->insertStmt->bindValue(3, $object->getDivision()->getId());
    $this->insertStmt->bindValue(4, $object->getAdmin());
    $this->insertStmt->bindValue(5, $object->getMailinglist());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getName());
    $this->insertStmt->bindValue(2, $object->getText());
    $this->insertStmt->bindValue(3, $object->getDivision()->getId());
    $this->insertStmt->bindValue(4, $object->getAdmin());
    $this->insertStmt->bindValue(5, $object->getMailinglist());
    $this->updateStmt->bindValue(6, $object->getId());
    
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

  function findByDivisionId($division_id) {
    $this->findByDivisionIdStmt->execute(array($division_id));
    return new App_Mapper_WorkShiftCollection($this->findByDivisionIdStmt->fetchAll(), $this);
  }

  function findByUserId($user_id) {
    $this->findByUserIdStmt->execute(array($user_id));
    return new App_Mapper_GroupCollection($this->findByUserIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new App_Mapper_GroupCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_Group";
  }
}

?>