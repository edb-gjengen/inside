<?php

/**
 * Add shift command class
 */
class App_Command_AddShift extends App_Command_Command {
  
  function doExecute(App_Controller_Request $request) {
    $title       = $request->getProperty("title");
    $starts      = $request->getProperty("starts_date") . " " . $request->getProperty("starts_time");
    $ends        = $request->getProperty("ends_date") . " " . $request->getProperty("ends_time");
    $num_workers = $request->getProperty("num_workers");
    $comment     = $request->getProperty("comment");
    
    $request->setProperty("location", 4); // glassbaren
    $groupid     = 97; // CNS bar
    $salaryType  = 3;
    
    $locations = App_Domain_Location::getFinder("App_Domain_Location")->findAll();
    $location = App_Domain_location::getFinder("App_Domain_Location")->find($request->getProperty("location"));
    
    $request->setObject("locations", $locations);
    
    if (is_null($request->getProperty("title"))) {
      // no user data given yet, show add shift page
      $request->setProperty("starts_date", date("j/n/Y"));
      $request->setProperty("starts_time", date("H:i"));
      $request->setProperty("ends_date", date("j/n/Y"));
      $request->setProperty("ends_time", date("H:i"));
      return self::statuses('CMD_DEFAULT');
    }
    
    if (empty($title)) {
      $request->addFeedback("Tittel må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
  
    if (empty($starts)) {
      $request->addFeedback("Starttid må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    } else {
      // change dd/mm/yyyy to dd.mm.yyyy to be on a valid date format
      $starts = str_replace("/", ".", $starts);
    }
  
    if (empty($ends)) {
      $request->addFeedback("Sluttid må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    } else {
      // change dd/mm/yyyy to dd.mm.yyyy to be on a valid date format
      $ends = str_replace("/", ".", $ends);
    }
  
    if (empty($num_workers)) {
      $request->addFeedback("Antall må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
    
    $salary_type_object = App_Domain_DomainObject::getFinder("App_Domain_WorkSalaryType")->find($salaryType);
    $group = App_Domain_DomainObject::getFinder("App_Domain_Group")->find($groupid);
    
    $shift = new App_Domain_WorkShift();
    $shift->setTitle($title);
    $shift->setStarts(new DateTime($starts));
    $shift->setEnds(new DateTime($ends));
    $shift->setActive(true);
    $shift->setComment($comment);
    $shift->setLocked(false);
    $shift->setAutoAssignWorkers(false);
    $shift->setNumWorkers($num_workers);
    $shift->setGroup($group);
    $shift->setSalaryType($salary_type_object);
    $shift->setLocation($location);
    
    return self::statuses('CMD_OK');
  }
  
}

?>