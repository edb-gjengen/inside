<?php

/**
 * Calendar class
 *
 * @version 0.1
 */

class App_Calendar_Calendar {
  private $date;
  private $month;
  private $startOfMonth;
  private $endOfMonth;
  
  function __construct(DateTime $date) {
    $this->fillCalendar($date);
  }
  
  function fillCalendar(DateTime $date) {
    $this->date = $date;
    
    // set startofmonth to first day of month
    $this->startOfMonth = new DateTime("2000-01-01");
    $this->startOfMonth->setDate($date->format('Y'), $date->format('m'), 1);
    
    // set endofmonth to last day of month
    $this->endOfMonth = new DateTime("2000-01-01");
    $this->endOfMonth->setDate($date->format('Y'), $date->format('m'), $date->format('t'));
    $this->month = new App_Calendar_Month($this->startOfMonth);
    
    // fill month with weeks
    $startofweek = new DateTime("2000-01-01");
    $startofweek->setISODate($this->startOfMonth->format('o'), $this->startOfMonth->format('W')); // gets the week number
    $startofweek->setTime(0, 0);
    while ($startofweek <= $this->endOfMonth) {
      $week = new App_Calendar_Week(new DateTime($startofweek->format('Y-m-d')));
      for ($i = 1; $i <= 7; $i++) {
        $startofday = new DateTime("2000-01-01");
        $startofday->setISODate($startofweek->format('o'), $startofweek->format('W'), $i);
        $day = new App_Calendar_Day($startofday);
        $week->addDay($day);
      }
      $this->month->addWeek($week);
      
      // add seven days to startofweek
      $startofweek->setISODate($startofweek->format('o'), $startofweek->format('W'), 8);
    }
  }
  
  function getMonth() {
    return $this->month;
  }
  
  function isInMonth(App_Calendar_Day $day) {
    if (($day->getDate() >= $this->startOfMonth) && ($day->getDate() <= $this->endOfMonth)) {
      return true;
    }
    return false;
  }
}

/**
 * Calendar month class
 *
 * @version 0.1
 */

class App_Calendar_Month {
  private $month;
  private $weeks = array();
  
  function __construct(DateTime $month) {
    $this->month = $month;
  }
  
  function getMonth() {
    return $this->month;
  }
  
  function setMonthName($name) {
    $this->monthName = $name;
  }
  
  function getMonthName() {
    return $this->monthName;
  }
  
  function addWeek(App_Calendar_Week $week) {
    $this->weeks[] = $week;
  }
  
  function getWeeks() {
    return $this->weeks;
  }
}

/**
 * Calendar week class
 *
 * @version 0.1
 */

class App_Calendar_Week {
  private $week;
  private $days = array();
  
  function __construct($week) {
    $this->week = $week;
  }
  
  function getWeek() {
    return $this->week;
  }
  
  function addDay(App_Calendar_Day $day) {
    $this->days[] = $day;
  }
  
  function getDays() {
    return $this->days;
  }
}

/**
 * Calendar day class
 *
 * @version 0.1
 */

class App_Calendar_Day {
  private $date;
  private $shifts = array();
  
  function __construct(DateTime $date) {
    $this->date = $date;
  }
  
  function getDate() {
    return $this->date;
  }
  
  function addShift(App_Domain_WorkShift $shift) {
    $this->shifts[] = $shift;
  }
  
  function getShifts() {
    return $this->shifts;
  }
}

?>