<?php
    $time_start = microtime(true);

    require_once "includes.php";

    //$ap = new ActionParser();



    $id = scriptParam("id");
    $prog = new WeekProgram($id);
    $mode = scriptParam("mode");

    define("EDIT_MODE", "edit");

    if ($mode == EDIT_MODE && !checkAuth("perform-register-weekprogram")) {
      if (!checkAuth("view-week-program")) {
          // ikke tilgang til å vise denne siden
          //$ap->performAction();
          $page = new Page();
          $page->display();
          exit();
      }
        // Ikke tilgang til å redigere
        $mode = "view";
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">

<head>
  <title>Ukesprogram for Det Norske Studentersamfund</title>
  <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />

<?php if ($mode == EDIT_MODE) { ?>
  <script type="text/javascript">
    var programid = <?php print $id; ?>;
    function update(callerid, target) {
        requestConcertInfo(callerid, target, programid);
    }

    function editText(container, text) {
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

    function updateText() {
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

    function editEndText(container, text) {
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

    function updateEndText() {
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

	<link href="http://www.studentersamfundet.no/css/weekprogrammail.css" rel="stylesheet" type="text/css" />
</head>

<body style="font-family: verdana, sans-serif; font-size: 11px; border: none; letter-spacing: 1px; border: none;">
	<div id="all" style="width: 600px; border: none; margin: 0 auto;">

<?php
	$gigs = new Concerts();
	if ($mode == EDIT_MODE) {
		$week = $prog->week - 1;
		$start = date("Y-m-d", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year"))));
		$picture = true;
?>
		<form method="post" action="ukesprogram.php">
			Topp 1: 
			<select name="main1" id="main1" onchange="update('main1', 'gig1');">
<?php
		// list gigs in selectbox 1
		$list = $gigs->getList($start, $picture);
		while ($gig = $list->fetchRow(DB_FETCHMODE_OBJECT)) {
			$gig = new Concert($gig->id);
?>
				<option value="<?php print $gig->id; ?>"<?php if ($gig->id == $prog->gig1) { print ('selected="selected"'); }?>>
					<?php print "$gig->time: $gig->name"; ?>
				</option>
<?php
		}
?>
			</select>
			<br />
  			Topp 2:
  			<select name="main2" id="main2" onchange="update('main2', 'gig2');">
<?php
		// list gigs in selectbox 2
		$list = $gigs->getList($start, $picture);
		while ($gig = $list->fetchRow(DB_FETCHMODE_OBJECT)) {
			$gig = new Concert($gig->id);
?>
				<option value="<?php print $gig->id; ?>"<?php if ($gig->id == $prog->gig2) { print ('selected="selected"'); }?>>
					<?php print "$gig->time: $gig->name"; ?>
				</option>
<?php
		}
?>
			</select>
			<br />
			Topp 3:
			<select name="main3" id="main3" onchange="update('main3', 'gig3');">
<?php
		// list gigs in selectbox 3
		$list = $gigs->getList($start, $picture);
		while ($gig = $list->fetchRow(DB_FETCHMODE_OBJECT)) {
			$gig = new Concert($gig->id);
?>
				<option value="<?php print $gig->id; ?>"<?php if ($gig->id == $prog->gig3) { print ('selected="selected"'); }?>>
					<?php print "$gig->time: $gig->name"; ?>
				</option>
<?php
		}
?>
  			</select>
  			<br />
  		</form>
<?php
	} // end of edit mode
?>


		<p>
			<img src="http://www.studentersamfundet.no/bilder/bannermail.png" alt="Det Norske Studentersamfund" border="0" />
		</p>

<?php

    print "<table class=\"top\">\n";
    print "<tr style=\"background: #fff;\">\n";

    for ($i = 0; $i < 3; $i++) {
        $gigno = "gig" . ($i+1);
        $gig = new Concert($prog->$gigno);

        print "<td id=\"" . $gigno . "\" style=\"vertical-align: center;\">\n";
        print "<div>\n";
        print "<h2 style=\"margin: 0px 3px; font-size: 14px;\">";
        print htmlentities($gig->name);
        print "</h2>\n";

        print "<p style=\"margin: 0px 3px; font-style: italic;\">";
        print strftime("%A %d. %B, %H:%M", strtotime($gig->time));
        print "</p>\n";
        print "</div>\n";

        print "<div style=\"margin-top: 5px; text-align: center; vertical-align: center; heigth: 250px;\">\n";

        if ($gig->picture) print "<img src=\"http://www.studentersamfundet.no/imageResize.php?pic=bilder/program/" . $gig->picture . "&amp;maxwidth=170\" alt=\"\" />\n";
        print "</div>\n";

        print "<div>\n";

        print "<p style=\"margin: 5px 3px 0;\">";
        print stripslashes($gig->intro);
        print "</p>\n";
        print "<p style=\"margin: 5px 3px;\">" .
            "<a href=\"http://www.studentersamfundet.no/vis.php?ID=" . $gig->id . "\">Les mer&raquo;</a></p>\n";
        print "</div>\n";
        print "</td>\n";
    }
    print "</tr>\n";
    print "</table>\n";

    print "<div class=\"clear\">&nbsp;</div>\n";

    if ($mode == EDIT_MODE) {
        print "<p><a href=\"javascript:editText('middle-section', 'middle-text');\">endre tekst:</a></p>\n";
    }

    print "<div id=\"middle-section\">\n";
    print "  <p id=\"middle-text\">\n";
    print nl2br($prog->pretext);
    print "  </p>\n";
    print "  <br />\n";
    print "</div>\n";

    print "<div id=\"concert-list\">\n";
    print "<table>\n";

	$week = $prog->week - 1;
	$start = date("Y-m-d", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year"))));
	$week = $prog->week;
	$end = date("Y-m-d 23:59", strtotime("last sunday", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $prog->year")))));
	$week_list = $gigs->getListWeek($start, $end);
	function month2maaned ($month) {
		switch ($month) {
			case 1:
				return "jan";
			case 2:
				return "februar";
			case 3:
				return "mars";
			case 4:
				return "april";
			case 5:
				return "mai";
			case 6:
				return "juni";
			case 7:
				return "juli";
			case 8:
				return "august";
			case 9:
				return "sept.";
			case 10:
				return "okt.";
			case 11:
				return "nov.";
			case 12:
				return "des.";
			default:
				return "";
		}
	}

    function day2dag ($day) {
        switch ($day) {
            case 0:
                return "søndag";
            case 1:
                return "mandag";
            case 2:
                return "tirsdag";
            case 3:
                return "onsdag";
            case 4:
                return "torsdag";
            case 5:
                return "fredag";
            case 6:
                return "lørdag";
            default:
                return "";
        }
    }

    $lastday = 0;
	// List all events this week
	while ($current = $week_list->fetchRow(DB_FETCHMODE_OBJECT)) {
		$gig = new Concert($current->id);

        // Choose if event should be view in week program
        if (isset($_POST["gigid"]) && ($_POST["gigid"] == $gig->id)) {
            if (isset($_POST["dontShow"]))
                $gig->viewWeekprogram = 0;
            elseif (isset($_POST["show"]))
                $gig->viewWeekprogram = 1;
            //print "update: gig=" . $gig->id . ", show=" . $gig->viewWeekprogram;
            $gig->store();
        }

        // Always show all events when we are in edit mode
        // if not, only show events which have viewWeekprogram enabled
        if ($mode == EDIT_MODE || $gig->viewWeekprogram) {

            // Print line with day ?
            if ($lastday != date("wm", strtotime($gig->time))) {
                if ($lastday) {
                    print "<tr height=\"5\" style=\"background: #ffffff;\">";
                    print "<td></td>";
                    print "</tr>\n";
                }
                print "<tr style=\"background: #e5e5e1; margin-top: 10px;\">\n";
                print "<td colspan=\"3\"><b>";
                print ucfirst(day2dag(date("w", strtotime($gig->time))));
                print date(" j. ", strtotime($gig->time));
                print month2maaned(date("m", strtotime($gig->time)));
                print "</b></td>\n";
                print "</tr>\n";
                $lastday = date("wm", strtotime($gig->time));
            }

            // Start table row
            print "<tr style=\"background: #f5f5f1;\">\n";

            // Print gig category and time
            //print "<td style=\"white-space: nowrap; vertical-align: top; padding: 3px 2px 0px 2px; border: none; width: 80px;\">\n";
            //print "<b>" . $gig->concertcategory_id . "</b>";
            //print "<br />";
            //print date("j. ", strtotime($gig->time)) . month2maaned(date("m", strtotime($gig->time))) . "<br /> kl " . date("H:i", strtotime($gig->time));
            //print "kl " . date("H:i", strtotime($gig->time));
            //print "</td>\n";

            // Print gig picture if defined
            print "<td style=\"vertical-align: center; align: center;\">\n";
            if ($gig->picture) {
                print "<img src=\"http://www.studentersamfundet.no/imageResize.php?pic=bilder/program/" . $gig->picture . "&amp;maxwidth=100\" alt=\"\" />\n";
            }
            print "</td>\n";

            // Print gig name and short info
            print "<td style=\"padding: 3px 0px 10px 5px; border: none; vertical-align: top;\">\n";
            print "<strong>" . prepareForHTML($gig->name) . "</strong>\n";
            print "<br />\n";
            print prepareForHTML($gig->intro);
            print " <a href=\"http://www.studentersamfundet.no/vis.php?ID=" . $gig->id . "\">Les mer&raquo;</a>";
            print "</td>\n";

            // Print gig place and price
            print "<td style=\"vertical-align: top; text-align: right; padding: 3px 2px 10px 2px; border: none;\">\n";
            print $gig->concertcategory_id . "<br /> ";
            print "<b>" . rtrim($gig->venue_name) . "</b>\n";
            print "<br />\n";
            print "kl " . date("H:i", strtotime($gig->time));
            print "<br />\n";
            if ($gig->priceNormal == 0 && $gig->priceConcession == 0) {
                print "Fri inngang";
            } else {
                print "Cc: " . $gig->priceNormal . "/" . $gig->priceConcession . "\n";
            }
            print "</td>\n";

            // Add edit stuff if we are in edit mode
            if ($mode == EDIT_MODE) {
                print "<td align=\"center\">";
                print "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "?id=$id&mode=" . EDIT_MODE . "\">";
                print "<input type=\"hidden\" name=\"gigid\" value=\"" . $gig->id . "\">";
                if ($gig->viewWeekprogram) {
                    print "<input type=\"submit\" name=\"dontShow\" value=\"ikke vis\">";
                } else {
                    print "<input type=\"submit\" name=\"show\" value=\"vis\">";
                }
                print "</form>";
                print "</td>\n";
            }

            // done with this gig's info
            print "</tr>\n";
        }
	}
?>
			</table>
		</div>

<?php 
	if ($mode == EDIT_MODE) {
?>
		<p><a href="javascript:editEndText('end-section', 'end-text');">endre tekst:</a></p>
<?php 
	} 
?>
		<div id="end-section">
			<br />
			<p id="end-text"><?php print nl2br($prog->posttext); ?></p>
			<br />
		</div>

		<div id="sponsors" style="text-align: center; border-top: 1px solid black;">
			<p>Våre samarbeidspartnere:</p>
			<a href="http://www.toro.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/toro.jpg" alt="Toro" /></a>
			<a href="http://www.sfnorge.no"><img style="margin: 12px; border: none;" src="http://www.studentersamfundet.no/bilder/sponsorer/sflogo.jpg" alt="SF Norge" /></a>
		</div>
		<div id="about" style="text-align: center; border-top: 1px solid black;">
			<p>Det Norske Studentersamfund</p>
			<p><a href="http://www.studentersamfundet.no/">www.studentersamfundet.no</a></p>
			<p>Chateau Neuf, Slemdalsveien 15, 0369 Oslo, tlf: 22 84 45 11</p>  
		</div>
	</div>
</body>
</html>
