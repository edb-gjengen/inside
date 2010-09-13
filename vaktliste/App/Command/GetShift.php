<?php

/**
 * Add shift command class
 */
class App_Command_GetShift extends App_Command_Command {
  
  function doExecute(App_Controller_Request $request) {
    if (is_null($request->getProperty("shift"))) {
      $request->addFeedback("Skift må oppgis");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
    
    $shift = App_Domain_WorkShift::getFinder("App_Domain_WorkShift")->find($request->getProperty("shift"));
    
    if (is_null($shift)) {
      $request->addFeedback("Fant ikke skiftet");
      return self::statuses('CMD_ERROR');
    }
    
    $request->setProperty("starts_date", $shift->getStarts()->format("j/n/Y"));
    $request->setProperty("starts_time", $shift->getStarts()->format("H:i"));
    $request->setProperty("ends_date", $shift->getEnds()->format("j/n/Y"));
    $request->setProperty("ends_time", $shift->getEnds()->format("H:i"));
    $request->setProperty("title", $shift->getTitle());
    $request->setProperty("num_workers", $shift->getNumWorkers());
    $request->setProperty("location", $shift->getLocation()->getId());
    
    $locations = App_Domain_Location::getFinder("App_Domain_Location")->findAll();
    $request->setObject("locations", $locations);
    
    return self::statuses('CMD_OK');
  }
  
}

?>