<?php

/**
 * User object mapper to database
 *
 * @version 0.1
 */

class mapper_UserUpdateMapper extends mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM din_userupdate WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM din_userupdate");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT * FROM din_userupdate WHERE user_id_updated=?");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_userupdate SET
        date=?,
        user_id_updated=?,
        comment=?,
        user_id_updated_by=?
        WHERE id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_userupdate (
        date, user_id_updated, comment, user_id_updated_by
        ) values (?,?,?,?)");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_userupdate WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new domain_UserUpdate($array["id"]);
    $obj->setDate($array["date"]);
    $obj->setUser($array["user_id_updated"]);
    $obj->setMessage($array["comment"]);
    $obj->setUpdatedBy($array["user_id_updated_By"]);
    return $obj;
  }

  protected function doInsert(domain_DomainObject $object) {
    $values = array($object->getUsername());
    //$this->insertStmt->execute($values);
    $id = self::$PDO->lastInsertId();
    $object->setId($id);
  }

  function update(domain_DomainObject $object) {
    $values = array(
      $object->getDate(),
      $object->getUser(),
      $object->getMessage(),
      $object->getUpdatedBy(),
      $object->getId()
      );
    print_r($values);
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
    return $this->deleteStmt();
  }

  function findByUserId($userid) {
    $this->findByUserIdStmt->execute(array($userid));
    return new mapper_UserUpdateCollection($this->findByUserIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new mapper_UserCollection($raw, $this);
  }

  protected function targetClass() {
    return "domain_UserUpdate";
  }
}

?>