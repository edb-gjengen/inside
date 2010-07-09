<?php

/**
 * Work shift worker object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_WorkShiftWorkerMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftworker WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftworker");
    $this->findByShiftIdStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftworker WHERE shift_id=?");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftworker WHERE user_id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO work_shiftworker (
        shift_id, user_id, assigned, no_show, user_comment, employer_comment
        ) values (?, ?, ?, ?, ?, ?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE work_shiftworker SET
        shift_id=?,
        user_id=?,
        assigned=?,
        no_show=?,
        user_comment=?,
        employer_comment=?
        WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM work_shiftworker WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_WorkShiftWorker($array["id"]);
    
    // find shift and create workshift object
    $shift_mapper = new App_Mapper_WorkShiftMapper();
    $shift = $shift_mapper->find($array["shift_id"]);
    $obj->setShift($shift);
    
    // find user and create user object
    $user_mapper = new App_Domain_User();
    $user = $user_mapper->find($array["user_id"]);
    $obj->setUser($user);
    
    $obj->setAssigned($array["assigned"]);
    $obj->setNoShow($array["no_show"]);
    $obj->setUserComment($array["user_comment"]);
    $obj->setEmployerComment($array["employer_comment"]);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getShift()->getId());
    $this->insertStmt->bindValue(2, $object->getUser()->getId());
    $this->insertStmt->bindValue(3, $object->getAssigned());
    $this->insertStmt->bindValue(4, $object->getNoShow());
    $this->insertStmt->bindValue(5, $object->getUserComment());
    $this->insertStmt->bindValue(6, $object->getEmployerComment());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getShift()->getId());
    $this->insertStmt->bindValue(2, $object->getUser()->getId());
    $this->insertStmt->bindValue(3, $object->getAssigned());
    $this->insertStmt->bindValue(4, $object->getNoShow());
    $this->insertStmt->bindValue(5, $object->getUserComment());
    $this->insertStmt->bindValue(6, $object->getEmployerComment());
    $this->updateStmt->bindValue(7, $object->getId());
    
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

  function findByShiftId($shift_id) {
    $this->findByShiftIdStmt->execute(array($shift_id));
    return new App_Mapper_WorkShiftWorkerCollection($this->findByShiftIdStmt->fetchAll(), $this);
  }

  function findByUserId($user_id) {
    $this->findByUserIdStmt->execute(array($user_id));
    return new App_Mapper_WorkShiftWorkerCollection($this->findByUserIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new App_Mapper_WorkShiftWorkerCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_WorkShiftWorker";
  }
}

?>