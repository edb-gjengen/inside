<?php

/**
 * Abstract mapper class for database mapping from domain
 *
 * @version 0.1
 */

abstract class App_Mapper_Mapper {
  protected static $PDO;
  
  const DATETIME_FORMAT = "Y-m-d H:i:s"; // mysql datetime format for DateTime class formatting

  function __construct() {
    if (!isset(self::$PDO)) {
      $dsn = App_Base_ApplicationRegistry::instance()->getDSN("PDO");
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

  private function getFromMap($id) {
    return App_Domain_ObjectWatcher::exists($this->targetClass(), $id);
  }

  private function addToMap(App_Domain_DomainObject $obj) {
    return App_Domain_ObjectWatcher::add($obj);
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
    $object->markClean();
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

  function insert(App_Domain_DomainObject $obj) {
    $this->doInsert($obj);
    $this->addToMap($obj);
    $obj->markClean();
  }

  function delete(App_Domain_DomainObject $obj) {
    $this->doDelete($obj);
    //$this->removeFromMap($obj);
  }

  abstract function update(App_Domain_DomainObject $object);
  protected abstract function doCreateObject(array $array);
  protected abstract function doInsert(App_Domain_DomainObject $object);
  protected abstract function doDelete(App_Domain_DomainObject $object);
  protected abstract function targetClass();
  protected abstract function selectStmt();
  protected abstract function selectAllStmt();
  protected abstract function deleteStmt();
  abstract function getCollection(array $raw);
}

?>