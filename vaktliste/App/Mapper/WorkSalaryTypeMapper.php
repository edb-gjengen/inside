<?php

/**
 * Work salary type object mapper to database table
 *
 * @version 0.1
 */

class App_Mapper_WorkSalaryTyperMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM work_salarytype WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM work_salarytype");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO work_salarytype (
        title, hourly_rate
        ) values (?, ?)");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE work_salarytype SET
        title=?,
        hourly_rate=?
        WHERE id=?");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM work_salarytype WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new App_Domain_WorkSalaryType($array["id"]);
    
    $obj->setTitle($array["title"]);
    $obj->setHourlyRate($array["hourly_rate"]);
    
    return $obj;
  }

  protected function doInsert(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getTitle());
    $this->insertStmt->bindValue(2, $object->getHourlyRate());
    
    $this->insertStmt->execute();
    $id = self::$PDO->lastInsertId();
    $this->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
    $this->insertStmt->bindValue(1, $object->getTitle());
    $this->insertStmt->bindValue(2, $object->getHourlyRate());
    $this->updateStmt->bindValue(3, $object->getId());
    
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
    return new App_Mapper_WorkSalaryTypeCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_WorkSalaryType";
  }
}

?>