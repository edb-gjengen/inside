<?php

/**
 * User object mapper to database
 *
 * @version 0.1
 */

class App_Mapper_UserMapper extends App_Mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT * FROM din_user WHERE id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT * FROM din_user");
    //$this->updateStmt = self::$PDO->prepare(
    //  "UPDATE din_user SET
    //    username=?,
    //    password=?,
    //    firstname=?,
    //    lastname=?,
    //    addresstype='no',
    //    valid_address=?,
    //    email=?,
    //    birthdate=?,
    //    placeOfStudy=?,
    //    passwordReset=?,
    //    division_id_request=?
    //    WHERE id=?");
    //$this->insertStmt = self::$PDO->prepare(
    //  "INSERT INTO din_user (
    //    cardno, username, password, firstname, lastname, addresstype, valid_address, email, birthdate, placeOfStudy, passwordReset, division_id_request 
    //    ) values (?)");
    //$this->deleteStmt = self::$PDO->prepare(
    //  "DELETE FROM din_user WHERE id=?");
    $this->findByLoginStmt = self::$PDO->prepare(
      "SELECT * FROM din_user WHERE username=? AND ( password=PASSWORD(?) or password=OLD_PASSWORD(?) )");
  }
  
  protected function doCreateObject(array $array) {
    $obj = new App_Domain_User($array["id"]);
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

  protected function doInsert(App_Domain_DomainObject $object) {
    $values = array($object->getUsername());
    throw new Excepetion("UserMapper::doInsert() failure: UserMapper is read only!");
    //$this->insertStmt->execute($values);
    //$id = self::$PDO->lastInsertId();
    //$object->setId($id);
  }

  function update(App_Domain_DomainObject $object) {
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
    throw new Excepetion("UserMapper:update() failure: UserMapper is read only!");
    //$this->updateStmt->execute($values);
  }

  protected function doDelete(App_Domain_DomainObject $object) {
    $values = array($object->getId());
    throw new Excepetion("UserMapper::doDelete() failure: UserMapper is read only!");
    //$this->deleteStmt->execute($values);
  }

  function selectStmt() {
    return $this->selectStmt;
  }

  function selectAllStmt() {
    return $this->selectAllStmt;
  }

  function deleteStmt() {
    throw new Excepetion("UserMapper::deleteStmt() failure: UserMapper is read only!");
    //return $this->deleteStmt;
  }

  function findByLogin($username, $password) {
    $this->findByLoginStmt->execute(array($username, $password, $password));
    $array = $this->findByLoginStmt->fetch();
    $this->findByLoginStmt->closeCursor();
    if (!is_array($array)) {
      return null;
    }
    if (!isset($array['id'])) {
      return null;
    }
    return $this->createObject($array);
  }

  function getCollection(array $raw) {
    return new App_Mapper_UserCollection($raw, $this);
  }

  protected function targetClass() {
    return "App_Domain_User";
  }
}

?>