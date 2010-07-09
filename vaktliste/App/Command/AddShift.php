<?php

/**
 * Add shift command class
 */
class App_Command_AddShift extends App_Command_Command {
  
  function doExecute(App_Controller_Request $request) {
    $title      = $request->getProperty("title");
    $starts     = $request->getProperty("starts");
    $ends       = $request->getProperty("ends");
    $numworkers = $request->getProperty("numworkers");
    $location   = $request->getProperty("location");
    $group      = 97; // CNS bar
    $salaryType = 3; // CNS bar
    
    if (empty($title)) {
      $request->addFeedback("Tittel må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
  
    if (empty($starts)) {
      $request->addFeedback("Starttid må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
  
    if (empty($ends)) {
      $request->addFeedback("Sluttid må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
  
    if (empty($numworkers)) {
      $request->addFeedback("Antall må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
    
    return self::statuses('CMD_OK');
  }
  
}

?>