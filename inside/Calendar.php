<?php

Class Calendar {
  var $type;

  function Calendar($type){
    $this->__construct($type);
  }

  public function __construct($type){
    $this->type = $type;
  }

  public function display($year, $month){
    $previousYear  = $year -1 . $month;
    $previousMonth = date("Ym", strtotime("-1 month", strtotime("$year-$month-01")));
    $nextMonth     = date("Ym", strtotime("+1 month", strtotime("$year-$month-01")));
    $nextYear      = $year +1 . $month;

    $fDate = "$year-$month-01";
    switch ($this->type){
    case 'events':
      $events = new Events();
      $list   = $events->getListMonth($year, $month);
      break;
 
    case 'concerts':
      $concerts = new Concerts();
      $list     = $concerts->getListMonth($year, $month);
      break;

    case 'all':
      $endMonth  = date("Y-m", strtotime("+1 month", strtotime("$year-$month-01")));
    	$sql = "SELECT id, name, DAYOFMONTH(time) AS date, 'event' AS type, time 
      	      FROM din_event
        	    WHERE time >= '$year-$month-01'
          	  AND time < '$endMonth-01'
            	UNION " .
             "SELECT id, tittel AS name, DAYOFMONTH(tid) AS date, 'concert' AS type, tid AS time 
      	      FROM program
        	    WHERE tid >= '$year-$month-01'
          	  AND tid < '$endMonth-01'
            	ORDER BY time ASC";
    	$conn = db_connect();
    	$list =& $conn->query($sql);
      break;

	case 'barshifts':
      $barshifts = new BarShifts();
      $list      = $barshifts->getListMonth($year, $month);
      break;

    default:
      return;
    }
      
    $dayOfWeek = date("w", strtotime("$year-$month-01"));
    if ($dayOfWeek == "0"){
      $dayOfWeek = 7;
    }
    $lastDayOfMonth = date("t", strtotime("$year-$month-01"));
    $weekNumber = date("W", strtotime("$year-$month-01"));

?>
    <div id="calHeader">
    	<div id="previous">
      	<a href="index.php?section=<?php print $this->type; ?>&amp;page=display-<?php print $this->type; ?>-calendar&amp;month=<?php print $previousYear; ?>">
        &lt;&lt;</a>
      	<a href="index.php?section=<?php print $this->type; ?>&amp;page=display-<?php print $this->type; ?>-calendar&amp;month=<?php print $previousMonth; ?>">
        &lt;</a>
    	</div>
    	<div id="next">
      	<a href="index.php?section=<?php print $this->type; ?>&amp;page=display-<?php print $this->type; ?>-calendar&amp;month=<?php print $nextMonth; ?>">
        &gt;</a>
      	<a href="index.php?section=<?php print $this->type; ?>&amp;page=display-<?php print $this->type; ?>-calendar&amp;month=<?php print $nextYear; ?>">
        &gt;&gt;</a>
    	</div>
    	<h2><?php print(strftime("%B - %Y", strtotime($fDate))); ?></h2>
    </div>
    <?php $this->_displayCalendarOptions(); ?>
    <table class="calendar">
      <tr>
        <th>Uke</th>
        <th>Mandag</th>
        <th>Tirsdag</th>
        <th>Onsdag</th>
        <th>Torsdag</th>
        <th>Fredag</th>
        <th>Lørdag</th>
        <th>Søndag</th>
      </tr>

      <tr>
        <td class="weekNumber"><?php print $weekNumber; ?></td>
<?php    
 
    $i = 1;
    $date = 1;
    $diff = $dayOfWeek;
    while ($i++ < $dayOfWeek){
      $offset = --$diff;
      printPassiveDay(date("d", strtotime("$year-$month-01 -$offset days")));
    }
    $row = $list->fetchRow(DB_FETCHMODE_ORDERED);
    while ($date <= $lastDayOfMonth){
      printOpenActiveDay($date);
      while ($row != NULL && $row[2] == $date){
      switch ($this->type){
      case 'events':
?>
          <div>
            <a href="index.php?section=<?php print $this->type; ?>&amp;page=display-event&amp;eventid=<?php print $row[0]; ?>">
              <?php print $row[1]; ?>
            </a>
                <!--<?php displayOptionsMenuCalendar($row[0], EVENT, "event", "view-edit-options-event", "$year$month"); ?>-->
          </div>
<?php        
       break;
   case 'concerts':
?>
          <div>
            <a href="index.php?section=<?php print $this->type; ?>&amp;page=display-concert&amp;concertid=<?php print $row[0]; ?>">
              <?php print $row[1]; ?>
            </a>
                <!--<?php displayOptionsMenuCalendar($row[0], CONCERT, "concert", "view-edit-options-concert", "$year$month"); ?>-->
          </div>
<?php    
			break;    
   case 'all':
?>
          <div class="calendar-<?php print $row[3]; ?>">
            <a href="index.php?section=<?php print $this->type; ?>&amp;page=display-<?php print $row[3]."&amp;".$row[3]."id=".$row[0]; ?>">
              <?php print $row[1]; ?>
            </a>
          </div>
<?php    
			break;    
   case 'barshifts':
		 $barshift = new BarShift($row[0]);
		 $barshift->displayCalendar($year, $month);
     break;
   
      }
        $row = $list->fetchRow(DB_FETCHMODE_ORDERED);
      }
      printCloseActiveDay();
      $date++;
      if ($i++ % 7 == 1){
        $weekNumber = date("W", strtotime("$year-$month-$date"));
        print("</tr><tr><td class=\"weekNumber\">".$weekNumber."</td>");            
      }
    }

    $date = 1;
    while ($i++ % 7 != 1){
      printPassiveDay($date++);
    }
    printPassiveDay($date++);

?>
     </tr>

    </table>
<?php
  }

	public
	function _displayCalendarOptions(){
		switch ($this->type){
    case 'events':
      break;
 
    case 'concerts':
      break;

    case 'all':
			?>
			<div>
				<label class="calendar-event btn" ><input type="checkbox" id="calendar-toggle-events" 
							onclick="toggleElements('div', 'calendar-event', this);"
							checked="checked" />
											 Interne arrangementer</label><br />
				<label class="calendar-concert btn"><input type="checkbox" id="calendar-toggle-concerts" 
							onclick="toggleElements('div', 'calendar-concert', this);"
							checked="checked" />
											 Åpne arrangementer</label>
			</div>
			<?php
      break;

		case 'barshifts':
			?>
			<div>
				<label class="calendar-barshifworker btn" ><input type="checkbox" id="calendar-toggle-barshiftworkers" 
							onchange="toggleElements('ul', 'barshift-workers', this);"
							checked="checked" />
											 Vis personell</label><br />
			</div>
			<?php
      break;

    default:
      return;
    }
		
	}

}

?>
