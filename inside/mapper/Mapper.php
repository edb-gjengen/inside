<?php

/**
 * Abstract mapper class for database mapping from domain
 *
 * @version 0.1
 */

abstract class mapper_Mapper {
  protected static $PDO;

  function __construct() {
    if (!isset(self::$PDO)) {
      $dsn = getDSN("PDO");
      if (is_null($dsn)) {
        throw new Exception("No DSN");
      }
      try {
        self::$PDO = new PDO($dsn["dsn"], $dsn["username"], $dsn["password"]);
        self::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (Exception $e) {
        throw new Exception("Database connection error: ". getDSN());
      }
    }
  }

  function find($id) {
    // check if object allready has been fetch from database
    $old = $this->getFromMap($id);
    if ($old) {
      return $old;
    }

    // get object from database
    $this->selectStmt()->execute(array($id));
    $array = $this->selectStmt()->fetch();
    $this->selectStmt()->closeCursor();
    if (!is_array($array)) {
      return null;
    }
    if (!isset($array['id'])) {
      return null;
    }
    $object = $this->createObject($array);
    return $object;
  }

  function findAll() {
    $this->selectAllStmt()->execute(array());
    return $this->getCollection( $this->selectAllStmt()->fetchAll( PDO::FETCH_ASSOC ) );
  }

  function createObject($array) {
    $old = $this->getFromMap($array['id']);
    if ($old) { return $old; }

    // construct object
    $obj = $this->doCreateObject($array);

    // add object to object watcher map
    $this->addToMap($obj);
    $obj->markClean();
    return $obj;
  }

  function insert(domain_DomainObject $obj) {
    $this->doInsert($obj);
    $this->addToMap($obj);
  }

  function delete(domain_DomainObject $obj) {
    $this->doDelete($obj);
    //$this->removeFromMap($obj);
  }

  private function getFromMap($id) {
    return domain_ObjectWatcher::exists($this->targetClass(), $id);
  }

  private function addToMap(domain_DomainObject $obj) {
    return domain_ObjectWatcher::add($obj);
  }

  abstract function update(domain_DomainObject $object);
  protected abstract function doCreateObject(array $array);
  protected abstract function doInsert(domain_DomainObject $object);
  protected abstract function doDelete(domain_DomainObject $object);
  protected abstract function targetClass();
  protected abstract function selectStmt();
  protected abstract function selectAllStmt();
  protected abstract function deleteStmt();
  abstract function getCollection(array $raw);
}
?>