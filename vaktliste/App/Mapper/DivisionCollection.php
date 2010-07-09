<?php

/**
 * Division collection - collection of elements in the din_division database table
 *
 * @version 0.1
 */

class App_Mapper_DivisionCollection extends App_Mapper_Collection {
  function targetClass() {
    return "App_Domain_Division";
  }
}

?>