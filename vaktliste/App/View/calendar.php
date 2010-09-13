<?php 

$request = App_View_ViewHelper::getRequest();
$currentUser = $request->getObject('currentUser');
$calendar = $request->getObject('calendar');

//$venue = $request->getObject('venue');

//$month = scriptParam("month");
if (!is_null($request->getProperty("month"))){
  $year  = substr($request->getProperty("month"), 0, 4);
  $month = substr($request->getProperty("month"), 5, 2);
} else {
  $year  = date("Y");
  $month = date("m");
}

// from calendar::display()
$previousYear  = ($year - 1) . "-" . $month;
$previousMonth = date("Y-m", strtotime("-1 month", strtotime("$year-$month-01")));
$nextMonth     = date("Y-m", strtotime("+1 month", strtotime("$year-$month-01")));
$nextYear      = ($year + 1) . "-" . $month;

$fDate = "$year-$month-01";

$dayOfWeek = date("w", strtotime("$year-$month-01"));
if ($dayOfWeek == "0"){
  $dayOfWeek = 7;
}
$lastDayOfMonth = date("t", strtotime("$year-$month-01"));
$weekNumber = date("W", strtotime("$year-$month-01"));

include("Common/header.php");
?>

<div>

  <h1>Vaktliste for <?=$calendar->getMonth()->getMonthName()?> <?=$year?></h1>
  
<?php
  print $request->getFeedbackString("<br />");
?>
  <div id="calHeader">
  	<div id="previous">
      <a href="<?=$_SERVER["PHP_SELF"]?>?cmd=<?=$request->getProperty('cmd')?>&month=<?php print $previousYear; ?>">&lt;&lt;</a>
      <a href="<?=$_SERVER["PHP_SELF"]?>?cmd=<?=$request->getProperty('cmd')?>&month=<?php print $previousMonth; ?>">&lt;</a>
    </div>
    <div id="next">
      <a href="<?=$_SERVER["PHP_SELF"]?>?cmd=<?=$request->getProperty('cmd')?>&month=<?php print $nextMonth; ?>">&gt;</a>
      <a href="<?=$_SERVER["PHP_SELF"]?>?cmd=<?=$request->getProperty('cmd')?>&month=<?php print $nextYear; ?>">&gt;&gt;</a>
    </div>
    
    <h2><?php print(strftime("%B - %Y", strtotime($fDate))); ?></h2>
  </div>

<?php //$calendar->_displayCalendarOptions(); ?>
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
<?php 

  foreach ($calendar->getMonth()->getWeeks() as $week) {
    print "<tr>";
    print "<td class=\"weekNumber\">" . $week->getWeek()->format("W") . "</td>";
    
    foreach ($week->getDays() as $day) {
      // is the date in current month ?
      if ($calendar->isInMonth($day)) {
        print "<td class=\"active\">";
      } else {
        print "<td class=\"passive\">";
      }
      print $day->getDate()->format("j");
      
      foreach ($day->getShifts() as $shift) {
        print "<div>";
        print $shift->getStarts()->format("H:i") . " <b>" . $shift->getTitle() . "</b> <a href=\"?work\">work</a>" . "  <a href=\"?edit-shift\">edit</a>";
        print "<br />";
        print $shift->getEnds()->format("H:i") . " <i>" . $shift->getLocation()->getName() . "</i>"; 
        print "</div>";
      }
      print "</td>";
    }
    print "</tr>\n";
  }
?>
  </table>
</div>

<?php 
include("Common/footer.php");
?>
