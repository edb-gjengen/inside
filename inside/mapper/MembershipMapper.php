<?php

/**
 * Membership object mapper to database
 *
 * @version 0.1
 */

class mapper_MembershipMapper extends mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT u.id as userId, u.cardno, u.expires FROM din_user u WHERE u.id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT u.id as userId, u.cardno, u.expires FROM din_user u");
    $this->updateStmt = self::$PDO->prepare(
      "UPDATE din_user SET
        cardno=?,
        expires=?,
        WHERE id=?");
//    $this->insertStmt = self::$PDO->prepare(
//      "UPDATE din_user (
//        cardno, username, password, firstname, lastname, addresstype, valid_address, email, birthdate, placeOfStudy, passwordReset, division_id_request 
//        ) values (?)");
    $this->deleteStmt = self::$PDO->prepare(
      "DELETE FROM din_user WHERE id=?");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT u.id as userId, u.cardno, u.expires FROM din_user u WHERE u.id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new domain_Membership($array["userId"]);
    
    $user_mapper = new mapper_UserMapper();
    $obj->setUser($user_mapper->find($array["userId"]));
    
    $card_mapper = new mapper_MembershipCardMapper();
    $obj->setCard($card_mapper->find($array["cardno"]));
    
    $obj->setCreated(new DateTime("-9999-12-31"));
    $obj->setStarts(new DateTime("-9999-12-31"));
    $obj->setExpires(new DateTime($array["expires"]));
    return $obj;
  }

  protected function doInsert(domain_DomainObject $object) {
//    $values = array($object->getUsername());
//    //$this->insertStmt->execute($values);
//    $id = self::$PDO->lastInsertId();
//    $object->setId($id);
  }

  function update(domain_DomainObject $object) {
    $values = array(
      $object->getCard()->getId(),
      $object->getExpires(),
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
    //return $this->deleteStmt;
  }

  function findByUserId($userid) {
    $this->findByUserIdStmt->execute(array($userid));
    return new mapper_MembershipCollection($this->findByUserIdStmt->fetchAll(), $this);
  }

  function findByCardId($userid) {
    //$this->findByCardIdStmt->execute(array($userid));
    //return new mapper_UserUpdateCollection($this->findByUserIdStmt->fetchAll(), $this);
    $this->selectStmt->execute(array($userid));
    return new mapper_MembershipCollection($this->selectStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new mapper_MembershipCollection($raw, $this);
  }

  protected function targetClass() {
    return "domain_Membership";
  }
}

?>