<?php

/**
 * Work shift worker collection - collection of elements in the work_shiftworker database table
 *
 * @version 0.1
 */

class App_Mapper_WorkShiftWorkerCollection extends App_Mapper_Collection {
  function targetClass() {
    return "App_Domain_WorkShiftWorker";
  }
}

?>