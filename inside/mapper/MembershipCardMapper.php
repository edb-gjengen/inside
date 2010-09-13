<?php

/**
 * MembershipCard object mapper to database
 *
 * @version 0.1
 */

//require_once( "mapper/Mapper.php" );
//require_once( "mapper/UserMapper.php" );
//require_once( "mapper/Collections.php" );

class mapper_MembershipCardMapper extends mapper_Mapper {

  function __construct() {
    // initialize database connection and common setup
    parent::__construct();

    // prepare SQL statements
    $this->selectStmt = self::$PDO->prepare(
      "SELECT c.id as id, u.id as userId, u.hasCard, u.lastSticker, c.date as created FROM din_usedcardno c LEFT JOIN din_user u ON c.id = u.cardno WHERE c.id=?");
    $this->selectAllStmt = self::$PDO->prepare(
      "SELECT u.id as userId, u.cardno as id, u.hasCard, u.lastSticker, c.date as created FROM din_user u LEFT JOIN din_usedcardno c ON u.cardno = c.id");
    $this->findByUserIdStmt = self::$PDO->prepare(
      "SELECT c.id as id, u.id as userId, u.hasCard, u.lastSticker, c.date as created FROM din_usedcardno c LEFT JOIN din_user u ON c.id = u.cardno WHERE u.id=?");
//    $this->updateStmt = self::$PDO->prepare(
//      "UPDATE din_user SET
//        cardno=?,
//        expires=?,
//        WHERE id=?");
//    $this->insertStmt = self::$PDO->prepare(
//      "INSERT INTO din_user (
//        expires
//        ) values (?)");
//    $this->deleteStmt = self::$PDO->prepare(
//      "DELETE FROM din_user WHERE id=?");
  }

  protected function doCreateObject(array $array) {
    $obj = new domain_MembershipCard($array["id"]);
    
    $user_mapper = new mapper_UserMapper();
    $obj->setUser($user_mapper->find($array["userId"]));
    
    $obj->setCreated(new DateTime($array["created"]));
    if ($array["hasCard"]) $obj->setProduced(new DateTime()); // set produced date to now if card has been produced
    $obj->setLastSticker($array["lastSticker"]);
    return $obj;
  }

  protected function doInsert(domain_DomainObject $object) {
  }

  function update(domain_DomainObject $object) {
  }

  protected function doDelete(domain_DomainObject $object) {
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
    return new mapper_MembershipCardCollection($this->findByUserIdStmt->fetchAll(), $this);
  }

  function getCollection(array $raw) {
    return new mapper_MembershipCardCollection($raw, $this);
  }

  protected function targetClass() {
    return "domain_MembershipCard";
  }
}

?>