<?php

header("Content-type: text/plain");
require_once "includes.php";
$id = scriptParam("id");
$prog = new WeekProgram($id);
?>

Ukesprogram for Det Norske Studentersamfund

<?php print html_entity_decode($prog->pretext); ?>

<?php
$gigs = new Concerts();


$week = $prog->week - 1;
$start = date("Y-m-d", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year"))));
$week = $prog->week;
$end = date("Y-m-d 23:59", strtotime("last sunday", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year")))));
$week_list = $gigs->getListWeek($start, $end);
?>
<?php

while ($current = $week_list->fetchRow(DB_FETCHMODE_OBJECT)) {
  $gig = new Concert($current->id);
?>


<?php print $gig->concertcategory_id; ?>: <?php print stripslashes(html_entity_decode($gig->name)); ?>

<?php print $gig->venue_name; ?> - <?php print date("d.m H:i", strtotime($gig->time)); ?>

Pris: <?php print $gig->priceNormal; ?>/<?php print $gig->priceConcession; ?>

<?php print stripslashes(html_entity_decode($gig->intro)); ?>

  <?php
}
?>

<?php print html_entity_decode($prog->posttext); ?>


Våre samarbeidspartnere:
Toro
Akademika

Det Norske Studentersamfund
www.studentersamfundet.no
Chateau Neuf, Slemdalsveien 15, 0369 Oslo, tlf: 22 84 45 11  
