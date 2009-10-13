<?php

header("Content-type: text/plain");
require_once "includes.php";
$id = scriptParam("id");
$prog = new ProgramSelection($id);
?>

<?php
if ($prog->type == "Billett") {
	print("Arrangementer med billettsalg på Det Norske Studentersamfund");
} else {
	print($prog->type."program for Det Norske Studentersamfund");
}?>


<?php print html_entity_decode(strip_tags(str_replace("<br />", "\n", $prog->pretext))); ?>

<?php
$gigs = new Concerts();


$start = $prog->start;
$end = $prog->end;
$type = $prog->type;
$list = $gigs->getListSelection($start, $end, $type);
?>
<?php

while ($current = $list->fetchRow(DB_FETCHMODE_OBJECT)) {
  $gig = new Concert($current->id);
?>

<?php print $gig->concertcategory_id; ?>: <?php print stripslashes(html_entity_decode($gig->name)); ?>

<?php print $gig->venue_name; ?> - <?php print date("d.m H:i", strtotime($gig->time)); ?>

Pris: <?php print $gig->priceNormal; ?>/<?php print $gig->priceConcession; ?>

<?php print stripslashes(html_entity_decode(strip_tags($gig->intro))); ?>

  <?php
}
?>

---

Våre samarbeidspartnere:

Sony Ericsson - Chess - NextGenTel - Toro - Radio 1 - Dagbladet - The Voice TV

---

Det Norske Studentersamfund
Chateau Neuf, Slemdalsveien 15, 0369 Oslo, tlf: 22 84 45 11  
