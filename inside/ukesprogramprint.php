<?php
$time_start = microtime(true);

require_once "includes.php";
if (checkAuth())
  $ap = new ActionParser();

$id = scriptParam("id");
$prog = new WeekProgram($id);
$mode = scriptParam("mode");
?>
<!DOCTYPE html>
<html lang="no">

<head>
  <title>Ukesprogram for Det Norske Studentersamfund</title>
  <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />

</head>
<body style="font-family: verdana, sans-serif; font-size: 11px; border: none; letter-spacing: 1px; border: none;">
  <div id="all" style="width: 800px; border: none; margin: 0 auto;">
<?php

$gigs = new Concerts();

?>


<div><img src="http://www.studentersamfundet.no/medlem/banner.png" alt="Det Norske Studentersamfund" /></div>
<div id="concert-list">
<?php
$week = $prog->week - 1;
$start = date("Y-m-d", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year"))));
$week = $prog->week;
$end = date("Y-m-d 23:59", strtotime("last sunday", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year")))));
$week_list = $gigs->getListWeek($start, $end);
?>
<table>
<?php

while ($current = $week_list->fetchRow(DB_FETCHMODE_OBJECT)) {
  $gig = new Concert($current->id);
?>
  <tr style="background: #EEE; font-size: 12px; font-weight: normal;">
    <td style="width: 82px; vertical-align: top; padding: 3px 2px 0px 2px; border: none;">
    	<?php print strftime("%a %d.%m kl. %H:%M", strtotime($gig->time)); ?></td>
    <td style="vertical-align: top; padding: 3px 2px 0px 2px; border: none;"><?php print $gig->concertcategory_id; ?></td>
    <td style="padding: 3px 0px 10px 5px; border: none;"><strong><a href="http://www.studentersamfundet.no/vis.php?ID=<?php print $gig->id; ?>"><?php print stripslashes($gig->name); ?></a></strong><br /><?php print stripslashes($gig->intro); ?></td>
    <td style="vertical-align: top; padding: 3px 2px 0px 2px; border: none;"><?php print $gig->venue_name; ?></td>
    <td style="width: 45px; vertical-align: top; text-align: center; padding: 3px 2px 0px 2px; border: none;"><?php print $gig->priceNormal; ?>/<?php print $gig->priceConcession; ?></td>
  </tr>
  <?php

}
?>
</table>
</div>

</body>
</html>
