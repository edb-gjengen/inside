<?php

/**
 * Work shift object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_WorkShiftMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM work_shift WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM work_shift");
    $this->findByGroupIdStmt = self::$PDO->prepare(
      "SELECT * FROM work_shift WHERE group_id=?");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE work_shift SET
        starts=?,
        ends=?,
        group_id=?,
        location_id=?,
        active=?,
        title=?,
        comment=?,
        num_workers=?,
        locked=?,
        autoassign_workers=?,
        salarytype_id=?
        WHERE id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO work_shift (
        starts, ends, group_id, location_id, active, title, comment, num_workers, locked, autoassign_workers, salarytype_id
        ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM work_shift WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_WorkShift($array["id"]);
    
    $obj->setStarts(new DateTime($array["starts"]));
    $obj->setEnds(new DateTime($array["ends"]));
    $obj->setActive($array["active"]);
    $obj->setTitle($array["title"]);
    $obj->setComment($array["comment"]);
    $obj->setNumWorkers($array["num_workers"]);
    $obj->setLocked($array["locked"]);
    $obj->setAutoAssignWorkers($array["autoassign_workers"]);
    
    // find group and create division object
    $group_mapper = new App_Domain_GroupMapper();
    $group = $group_mapper->find($array["group_id"]);
    $obj->setGroup($group);
    
    // find location and create location object
    $location_mapper = new App_Domain_LocationMapper();
    $location = $location_mapper->find($array["location_id"]);
    $obj->setLocation($location);
    
    // find salary type and create salary type object
    $salarytype_mapper = new App_Domain_WorkSalaryTypeMapper();
    $salarytype = $salarytype_mapper->find($array["slarytype_id"]);
    $obj->setSalaryType($salarytype);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getStarts()->format(self::DATETIME_FORMAT));
    $this->insertStmt->bindValue(2, $object->getEnds()->format(self::DATETIME_FORMAT));
    $this->insertStmt->bindValue(3, $object->getGroup()->getId());
    $this->insertStmt->bindValue(4, $object->getLocation()->getId());
    $this->insertStmt->bindValue(5, $object->getActive());
    $this->insertStmt->bindValue(6, $object->getTitle());
    $this->insertStmt->bindValue(7, $object->getComment());
    $this->insertStmt->bindValue(8, $object->getNumWorkers());
    $this->insertStmt->bindValue(9, $object->getLocked());
    $this->insertStmt->bindValue(10, $object->getAutoAssignWorkers());
    $this->insertStmt->bindValue(11, $object->getSalaryType()->getId());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->updateStmt->bindValue(1, $object->getStarts()->format(self::DATETIME_FORMAT));
    $this->updateStmt->bindValue(2, $object->getEnds()->format(self::DATETIME_FORMAT));
    $this->updateStmt->bindValue(3, $object->getGroup()->getId());
    $this->updateStmt->bindValue(4, $object->getLocation()->getId());
    $this->updateStmt->bindValue(5, $object->getActive());
    $this->updateStmt->bindValue(6, $object->getTitle());
    $this->updateStmt->bindValue(7, $object->getComment());
    $this->updateStmt->bindValue(8, $object->getNumWorkers());
    $this->updateStmt->bindValue(9, $object->getLocked());
    $this->updateStmt->bindValue(10, $object->getAutoAssignWorkers());
    $this->updateStmt->bindValue(11, $object->getSalaryType()->getId());
    $this->updateStmt->bindValue(12, $object->getId());
    
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

  function findByGroupId($group_id) {
    $this->findByGroupIdStmt->execute(array($group_id));
    return new App_Mapper_WorkShiftCollection($this->findByGroupIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new App_Mapper_WorkShiftCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_WorkShift";
  }
}

?>