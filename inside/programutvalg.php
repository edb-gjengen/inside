<?php
$time_start = microtime(true);

require_once "includes.php";

$id = scriptParam("id");
$prog = new ProgramSelection($id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">

<head>
  <title>Programutvalg for Det Norske Studentersamfund</title>
  <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
</head>

<body style="font-family: verdana, sans-serif; font-size: 11px; letter-spacing: 1px;">
  <div id="all" style="width: 800px; margin: 0 auto;">
<?php

$gigs = new Concerts();

?>


<h1><img src="http://www.studentersamfundet.no/medlem/banner.png" alt="Det Norske Studentersamfund" /></h1>

<div id="middle-section">
<p id="middle-text"><?php print nl2br($prog->pretext); ?></p><br />
</div>

<div id="concert-list">
<?php


$start = $prog->start;
$end   = $prog->end;
$type  = $prog->type;
$list  = $gigs->getListSelection($start, $end, $type);
?>
<table>
<?php

while ($current = $list->fetchRow(DB_FETCHMODE_OBJECT)) {
  $gig = new Concert($current->id);
?>
  <tr style="background: #EEE;">
    <td style="padding: 3px 2px 10px 2px; text-align: right; vertical-align: top;"><img src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/program/<?php print $gig->picture;?>&amp;maxwidth=150" alt="" /></td>
    <td style="padding: 3px 2px 10px 2px; vertical-align: top;"><strong><a href="http://www.studentersamfundet.no/vis.php?ID=<?php print $gig->id; ?>"><?php print stripslashes($gig->name); ?></a></strong> - <?php print date("d.m H:i", strtotime($gig->time)); ?><br /><?php print stripslashes($gig->intro); ?></td>
    <td style="padding: 3px 2px 10px 2px; vertical-align: top;"><?php print $gig->venue_name; ?></td>
    <td style="padding: 3px 2px 10px 2px; white-space: nowrap; text-align: center; vertical-align: top;"><?php print $gig->priceNormal; ?>/<?php print $gig->priceConcession; ?></td>
  </tr>
  <?php

}
?>
</table>
</div>


<div id="sponsors" style="margin-top: 10px; border-top: 1px solid black; text-align: center;">
<p>V�re samarbeidspartnere:</p>
	<a href="http://www.chess.no"><img src="http://www.studentersamfundet.no/bilder/sponsorer/chess.png" alt="Chess" /></a>
  <a href="http://www.nextgentel.no"><img src="http://www.studentersamfundet.no/bilder/sponsorer/nextgentel.gif" alt="NextGenTel" /></a>
  <a href="http://www.toro.no"><img src="http://www.studentersamfundet.no/bilder/sponsorer/toro.jpg" alt="Toro" /></a>
  <a href="http://www.sonyericsson.com"><img src="http://www.studentersamfundet.no/bilder/sponsorer/logo_sony.gif" alt="Sony Ericsson" /></a>
  <a href="http://www.dagbladet.no"><img src="http://www.studentersamfundet.no/bilder/sponsorer/dagbladet.gif" alt="Dagbladet" /></a>
  <a href="http://radio1.no"><img src="http://www.studentersamfundet.no/bilder/sponsorer/radio1.gif" alt="Radio 1" /></a>
  <a href="http://thevoice.no"><img src="http://www.studentersamfundet.no/bilder/sponsorer/the_voice.gif" alt="The Voice TV" /></a>
</div>
<div id="about" style="margin-top: 10px; border-top: 1px solid black; text-align: center;">
<p>Det Norske Studentersamfund</p>
<p><a href="http://www.studentersamfundet.no/">www.studentersamfundet.no</a></p>
<p>Chateau Neuf, Slemdalsveien 15, 0369 Oslo, tlf: 22 84 45 11</p>  
</div>
</div>
</body>
</html>