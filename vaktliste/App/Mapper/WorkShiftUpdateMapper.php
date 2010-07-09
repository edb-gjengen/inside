<?php

/**
 * Work shift update object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_WorkShiftUpdateMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftupdate WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftupdate");
    $this->findByShiftIdStmt = self::$PDO->prepare(
      "SELECT * FROM work_shiftupdate WHERE shift_id=?");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE work_shiftupdate SET
      	shift_id=?,
      	update_time=?,
      	user_id=?,
      	update_text=?
        WHERE id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO work_shiftupdate (
        shift_id, update_time, user_id, update_text
        ) values (?, ?, ?, ?)");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM work_shiftupdate WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_WorkShiftUpdate($array["id"]);
    
    // find shift and create workshift object
    $shift_mapper = new App_Mapper_WorkShiftMapper();
    $shift = $shift_mapper->find($array["shift_id"]);
    $obj->setShift($shift);
    
    $obj->setUpdateTime(new DateTime($array["update_time"]));
    
    // find user and create user object
    $user_mapper = new App_Domain_User();
    $user = $user_mapper->find($array["user_id"]);
    $obj->setUser($user);
    
    $obj->setUpdateText($array["text"]);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getShift()->getId());
    $this->insertStmt->bindValue(2, $object->getUpdateTime()->format(self::DATETIME_FORMAT));
    $this->insertStmt->bindValue(3, $object->getUser()->getId());
    $this->insertStmt->bindValue(4, $object->getUpdateText());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getShift()->getId());
    $this->insertStmt->bindValue(2, $object->getUpdateTime()->format(self::DATETIME_FORMAT));
    $this->insertStmt->bindValue(3, $object->getUser()->getId());
    $this->insertStmt->bindValue(4, $object->getUpdateText());
    $this->updateStmt->bindValue(5, $object->getId());
    
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
    return new App_Mapper_WorkShiftUpdateCollection($this->findByShiftIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new App_Mapper_WorkShiftUpdateCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_WorkShiftUpdate";
  }
}

?>