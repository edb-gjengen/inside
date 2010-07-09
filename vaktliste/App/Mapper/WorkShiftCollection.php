<?php

/**
 * Work shift collection - collection of elements in the work_shift database table
 *
 * @version 0.1
 */

class App_Mapper_WorkShiftCollection extends App_Mapper_Collection {
  function targetClass() {
    return "App_Domain_WorkShift";
  }
}

?>