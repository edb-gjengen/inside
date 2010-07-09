<?php

/**
 * Helper factory for fetching collections from the database mapper
 *
 * @version 0.1
 */

class App_Domain_HelperFactory {
  const DIVISION = "App_Domain_Division";
  const LOCATION = "App_Domain_Location";
  const USER = "App_Domain_User";
  const USER_PHONE = "App_Domain_UserPhone";
  const WORK_SALARY_TYPE = "App_Domain_WorkSalaryType";
  const WORK_SHIFT = "App_Domain_WorkShift";
  const WORK_SHIFT_UPDATE = "App_Domain_WorkShiftUpdate";
  const WORK_SHIFT_WORKER = "App_Domain_WorkShiftWorker";

  function getCollection($type) {
    switch ($type) {
      case (self::DIVISION):
        return new App_Mapper_DivisionCollection();
      case (self::LOCATION):
        return new App_Mapper_LocationCollection();
      case (self::USER):
        return new App_Mapper_UserCollection();
      case (self::USER_PHONE):
        return new App_Mapper_UserPhoneCollection();
      case (self::WORK_SALARY_TYPE):
        return new App_Mapper_WorkSalaryTypeCollection();
      case (self::WORK_SHIFT):
        return new App_Mapper_WorkShiftCollection();
      case (self::WORK_SHIFT_UPDATE):
        return new App_Mapper_WorkShiftUpdateCollection();
      case (self::WORK_SHIFT_WORKER):
        return new App_Mapper_WorkShiftWorkerCollection();
      default:
        throw new Exception("Unknown collection type $type");
    }
  }

  function getFinder($type) {
    switch ($type) {
      case (self::DIVISION):
        return new App_Mapper_DivisionMapper();
      case (self::LOCATION):
        return new App_Mapper_LocationMapper();
      case (self::USER):
        return new App_Mapper_UserMapper();
      case (self::USER_PHONE):
        return new App_Mapper_UserPhoneMapper();
      case (self::WORK_SALARY_TYPE):
        return new App_Mapper_WorkSalaryTypeMapper();
      case (self::WORK_SHIFT):
        return new App_Mapper_WorkShiftMapper();
      case (self::WORK_SHIFT_UPDATE):
        return new App_Mapper_WorkShiftUpdateMapper();
      case (self::WORK_SHIFT_WORKER):
        return new App_Mapper_WorkShiftWorkerMapper();
      default:
        throw new Exception("Unknown finder type $type");
    }
  }
}

?>