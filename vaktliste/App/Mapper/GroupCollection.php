<?php

/**
 * Group collection - collection of elements in the din_group database table
 *
 * @version 0.1
 */

class App_Mapper_GroupCollection extends App_Mapper_Collection {
  function targetClass() {
    return "App_Domain_Group";
  }
}

?>