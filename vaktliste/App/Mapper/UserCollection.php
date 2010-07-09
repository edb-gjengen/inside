<?php

/**
 * User collection - collection of elements in the user database
 *
 * @version 0.1
 */

class App_Mapper_UserCollection extends App_Mapper_Collection {
  function targetClass() {
    return "App_Domain_User";
  }
}
?>