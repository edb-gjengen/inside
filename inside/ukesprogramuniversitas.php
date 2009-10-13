<?php
/*
header("Content-type: text/plain");
require_once "includes.php";
$id = scriptParam("id");
$prog = new WeekProgram($id);
?>

Ukesprogram for Det Norske Studentersamfund

<?php
$gigs = new Concerts();

$week = $prog->week - 1;
$start = date("Y-m-d", strtotime("+$week week", strtotime("wednesday", strtotime(" 1 jan $prog->year"))));
$week = $prog->week;
$end = date("Y-m-d 23:59", strtotime("+$prog->week week", strtotime("wednesday", strtotime(" 1 jan $prog->year"))));
$week_list = $gigs->getListWeek($start, $end);
?>
<?php

while ($current = $week_list->fetchRow(DB_FETCHMODE_OBJECT)) {
  $gig = new Concert($current->id);
?>


@plakdag:<?php print strftime("%A %d.", strtotime($gig->time)); ?>

@plaktxt:<b><?php print $gig->host_name; ?></b><i><?php 
	print stripslashes(html_entity_decode($gig->name)); ?> - <?php 
	print stripslashes(html_entity_decode($gig->intro)); ?></i>
<?php print $gig->venue_name; ?>, <?php print date("H:i", strtotime($gig->time)); ?>. <?php print $gig->priceNormal; ?>/<?php print $gig->priceConcession; ?>
<?php
}
*/

$time_start = microtime(true);

require_once "includes.php";
if (checkAuth())
  $ap = new ActionParser();

$id = scriptParam("id");
$prog = new WeekProgram($id);
$gigs = new Concerts();

$week = $prog->week - 1;
$start = date("Y-m-d", strtotime("+$week week", strtotime("wednesday", strtotime(" 1 jan $prog->year"))));
$week = $prog->week;
$end = date("Y-m-d 23:59", strtotime("+$prog->week week", strtotime("tuesday", strtotime(" 1 jan $prog->year"))));
$week_list = $gigs->getListWeek($start, $end);

$mode = scriptParam("mode");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">

<head>
  <title>Ukesprogram for Det Norske Studentersamfund</title>
  <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />

<?php if ($mode == 'edit'){ ?>
  <script type="text/javascript">
    var programid = <?php print $id; ?>;
    function update(callerid, target){
      requestConcertInfo(callerid, target, programid);
    }
    
    function editText(container, text){
      var oForm        = document.createElement('textarea');
      oForm.setAttribute("rows", 10);
      oForm.setAttribute("cols", 80);
      oForm.setAttribute("id", "currentForm");

      var bUpdate      = document.createElement('input');
      bUpdate.setAttribute("type", "button");
      bUpdate.setAttribute("value", "oppdater");
      bUpdate.setAttribute("id", "middle-button");
      bUpdate.setAttribute("onclick", "updateText('currentForm')");
      
      var divContainer = document.getElementById(container);
      var pTarget      = document.getElementById(text);
      var sText        = pTarget.innerHTML;
      oForm.innerHTML  = sText;
      divContainer.replaceChild(oForm, pTarget);
      divContainer.appendChild(bUpdate);
    }
    
    function updateText(){
      var oForm = document.getElementById('currentForm');
      var sText = oForm.value;
      setWeekText(sText, <?php print $id; ?>, "pre");

      var pTarget = document.createElement('p');
      pTarget.setAttribute("id", "middle-text");
      pTarget.innerHTML = sText;

      var divMiddle = document.getElementById("middle-section");
      divMiddle.removeChild(document.getElementById('middle-button'));
      divMiddle.replaceChild(pTarget, oForm);
    }

    function editEndText(container, text){
      var oForm        = document.createElement('textarea');
      oForm.setAttribute("rows", 10);
      oForm.setAttribute("cols", 80);
      oForm.setAttribute("id", "currentEndForm");

      var bUpdate      = document.createElement('input');
      bUpdate.setAttribute("type", "button");
      bUpdate.setAttribute("value", "oppdater");
      bUpdate.setAttribute("id", "end-button");
      bUpdate.setAttribute("onclick", "updateEndText('currentEndForm')");
      
      var divContainer = document.getElementById(container);
      var pTarget      = document.getElementById(text);
      var sText        = pTarget.innerHTML;
      oForm.innerHTML  = sText;
      divContainer.replaceChild(oForm, pTarget);
      divContainer.appendChild(bUpdate);
    }
    
    function updateEndText(){
      var oForm = document.getElementById('currentEndForm');
      var sText = oForm.value;

      setWeekText(sText, <?php print $id; ?>, "post");

      var pTarget = document.createElement('p');
      pTarget.setAttribute("id", "end-text");
      pTarget.innerHTML = sText;

      var divEnd = document.getElementById("end-section");
      divEnd.removeChild(document.getElementById('end-button'));
      divEnd.replaceChild(pTarget, oForm);
    }

  </script>
  <script type="text/javascript" src="ajax.js"></script>
  <script type="text/javascript" src="../includes/zXml/zxml.js"></script>
  
<?php } ?>  
</head>
<body style="font-family: verdana, sans-serif; font-size: 11px; border: none; letter-spacing: 1px; border: none;">
  <div id="all" style="width: 800px; border: none; margin: 0 auto;">

<h1><img src="http://www.studentersamfundet.no/medlem/banner.png" alt="Det Norske Studentersamfund" /></h1>

<div id="concert-list">
<?php

?>
<table>
<?php

while ($current = $week_list->fetchRow(DB_FETCHMODE_OBJECT)) {
  $gig = new Concert($current->id);
?>
  <tr style="background: #EEE;">
    <td style="white-space: nowrap; vertical-align: top; padding: 3px 2px 0px 2px; border: none;"><?php print date("d/m", strtotime($gig->time)); ?></td>
    <td style="vertical-align: top; padding: 3px 2px 0px 2px; border: none;"><?php print $gig->concertcategory_id; ?>: <strong><a href="http://www.studentersamfundet.no/vis.php?ID=<?php print $gig->id; ?>"><?php print prepareForHTML($gig->name); ?></a>.</strong></td>
    <td style="white-space: nowrap; vertical-align: top; padding: 3px 2px 0px 2px; border: none;"><?php print date("H:i", strtotime($gig->time)); ?>, <?php print $gig->venue_name; ?>, <?php print $gig->priceNormal; ?>,-/<?php print $gig->priceConcession; ?>,-</td>
  </tr>
  <?php

}
?>
</table>
</div>

<?php if ($mode == 'edit') { ?>
   <p><a href="javascript:editEndText('end-section', 'end-text');">endre tekst:</a></p><?php } ?>
<div id="end-section">
<br />
<p id="end-text"><?php print nl2br($prog->posttext); ?></p>
<br />
</div>


<div id="sponsors" style="text-align: center; border-top: 1px solid black;">
<p>Våre samarbeidspartnere:</p>
	<a href="http://www.chess.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/chess.png" alt="Chess" /></a>
	<a href="http://www.samsung.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/samsung.png" alt="Samsung" /></a>
  <a href="http://www.toro.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/toro.jpg" alt="Toro" /></a>
	<a href="http://www.nescafe.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/nescafe.png" alt="Nescafé" /></a>
  <a href="http://www.dagbladet.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/dagbladet.gif" alt="Dagbladet" /></a>
	<a href="http://www.kanal24.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/kanal24.png" alt="Kanal 24" /></a>
</div>
<div id="about" style="text-align: center; border-top: 1px solid black;">
<p>Det Norske Studentersamfund</p>
<p><a href="http://www.studentersamfundet.no/">www.studentersamfundet.no</a></p>
<p>Chateau Neuf, Slemdalsveien 15, 0369 Oslo, tlf: 22 84 45 11</p>  
</div>
</div>
</body>
</html>