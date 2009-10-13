<?php

require_once "includes.php";

$gigid = scriptParam("gigid");

$gig = new Concert($gigid);
$store     = scriptParam("store");
$programid = scriptParam("programid");


if ($store > 0){
  $sql = "UPDATE weekprogram SET " .
         "  gig$store = $gigid " .
         "WHERE " .
         "  id = $programid";
  $conn = db_connect();
  $result =& $conn->query($sql);
  
}

?>
<div style="text-align: center;">
	<img src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/program/<?php print $gig->picture;?>&amp;maxwidth=150" alt="" />
</div>
	<h2 style="margin: 0px 3px; font-size: 14px;"><?php print htmlentities($gig->name); ?></h2>
	<p style="margin: 0px 3px; font-style: italic;"><?php print strftime("%A %d. %B, %H:%M", strtotime($gig->time)); ?></p>
	<p style="margin: 5px 3px 0;"><?php print stripslashes($gig->intro); ?></p>
	<p style="margin: 5px 3px;"><a href="http://www.studentersamfundet.no/vis.php?ID=<?php print $gig->id; ?>">Les mer&raquo;</a></p>
