<?php

/**
 * Work salary type collection - collection of elements in the work_salarytype database table
 *
 * @version 0.1
 */

class App_Mapper_WorkSalaryTypeCollection extends App_Mapper_Collection {
  function targetClass() {
    return "App_Domain_WorkSalaryType";
  }
}

?>