<?php

/**
 * User object mapper to database
 *
 * @version 0.1
 */

class mapper_UserMapper extends mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM din_user WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM din_user");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_user SET
        username=?,
        password=?,
        firstname=?,
        lastname=?,
        addresstype='no',
        valid_address=?,
        email=?,
        birthdate=?,
        placeOfStudy=?,
        passwordReset=?,
        division_id_request=?
        WHERE id=?");
    $this->insertStmt = self::$PDO->prepare(
      "INSERT INTO din_user (
        username
        ) values (?)");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_user WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new domain_User($array["id"]);
    $obj->setUsername($array["username"]);
    $obj->setPassword($array["password"]);
    $obj->setFirstname($array["firstname"]);
    $obj->setLastname($array["lastname"]);
    $obj->setValidaddress($array["valid_address"]);
    $obj->setEmail($array["email"]);
    $obj->setBirthdate($array["birthdate"]);
    $obj->setPlaceOfStudy($array["placeOfStudy"]);
    $obj->setPasswordreset($array["passwordReset"]);
    $obj->setDivisionIdRequest($array["division_id_request"]);
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
      $object->getUsername(),
      $object->getPassword(),
      $object->getFirstname(),
      $object->getLastname(),
      $object->getValidAddress(),
      $object->getEmail(),
      $object->getBirthdate(),
      $object->getPlaceOfStudy(),
      $object->getPasswordreset(),
      $object->getDivisionIdRequest(),
      $object->getId()
      );
    print_r($values);
    //$this->updateStmt->execute($values);
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

  function getCollection(array $raw) {
    return new mapper_UserCollection($raw, $this);
  }

  protected function targetClass() {
    return "domain_User";
  }
}

?>