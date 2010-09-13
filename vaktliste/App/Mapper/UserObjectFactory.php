<?php

class App_Mapper_UserObjectFactory extends App_Mapper_DomainObjectFactory {
  function createObject ( array $array ) {
    $obj = new App_Domain_User($array['id']);
    $obj->setname($array['name']);
    return $obj;
  }
}