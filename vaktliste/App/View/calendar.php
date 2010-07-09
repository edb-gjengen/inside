<?php 

$request = App_View_ViewHelper::getRequest();
$currentUser = $request->getObject('currentUser');
//$venue = $request->getObject('venue');

include_once("Calendar.php");
$calendar = new Calendar("barshifts");
//$month = scriptParam("month");
if (!empty($month)){
  $year  = substr($month, 0, 4);
  $month = substr($month, 4, 2);
}else {
  $year  = date("Y");
  $month = date("m");
}

include("Common/header.php");
?>

<div>

<?php

  print "<h1>Vaktliste for " . $month . " " . $year . "</h1>\n";

  print $request->getFeedbackString("<br />");
  
  $calendar->display($year, $month);
  
?>

</div>

<?php 
include("Common/footer.php");
?>
