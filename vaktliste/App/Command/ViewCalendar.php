<?php

/**
 * View calendar command class
 */
class App_Command_ViewCalendar extends App_Command_Command {
  
  function doExecute(App_Controller_Request $request) {
    if (is_null($request->getProperty("month"))) {
      // no month given, show current month
      $calendar = new App_Calendar_Calendar(new DateTime());
    } else {
      $calendar = new App_Calendar_Calendar(new DateTime($request->getProperty("month")."-01"));
    }
    
    // Set month name
    $calendar->getMonth()->setMonthName($calendar->getMonth()->getMonth()->format("F"));
    
    // Add shifts to calendar
    $shifts = App_Domain_Location::getFinder("App_Domain_WorkShift")->findAll();
    $shifts->rewind();
    
    // initialization
    $shift = $shifts->next();
    
    foreach ($calendar->getMonth()->getWeeks() as $week) {
      foreach ($week->getDays() as $day) {
        // end of day in PHP 5.2.0
        $dayends = clone $day->getDate();
        $dayends->modify("+1 day");
        
        while ($shifts->valid() && ($shift->getStarts() < $dayends)) {
          //while ($shifts->valid() && ($shift->getStarts() < $day->getDate()->add(new DateInterval('P1D'))) { // Requires PHP >= 5.3.0
          
          // Skip shifts that starts before current day
          if ($shift->getStarts() >= $day->getDate()) {
            $day->addShift($shift);
          }
          
          // go to next shift
          $shift = $shifts->next();
        }
      }
    }
    
    $request->setObject('calendar', $calendar);
    
    return self::statuses('CMD_DEFAULT');
  }
  
}

?>