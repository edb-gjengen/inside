
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">

  <head>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <link rel="stylesheet" title="default" type="text/css" href="https://www.studentersamfundet.no/inside/screen.css" />
    <title><?php print App_Base_ApplicationRegistry::getPageTitle(); ?></title>
  </head>

  <body class="dns section-inside">
    <div id="container">
      <div id="header">
        <span class="site-title"><a href="http://studentersamfundet.no/"><?php print App_Base_ApplicationRegistry::getPageTitle(); ?></a></span>

        <div id="quick-menu">
            Om Studentersamfundet | <a href="http://studentersamfundet.no/kart.php">Kart</a> | <a href="http://studentersamfundet.no/kontakt.php">Kontakt</a>
        </div>

        <div class="aapningstider">
            <table>
                <tr><td>Mandag-onsdag</td><td>13-01</td></tr>
                <tr><td>Torsdag-fredag</td><td>13-03</td></tr>
                <tr><td>L&oslash;rdag</td><td>16-03</td></tr>
                <tr><td>Kjøkkenet</td><td>Til 19</td></tr>
            </table>
        </div>

        <div id="menu">
            <ul>
                <li id="forside-meny"><a href="http://studentersamfundet.no/">Forside</a>
                    <div class="sub-menu">
                        <ul>
                            <li><a href="http://studentersamfundet.no/lokaler.php">Lokaler</a></li>
                            <li><a href="http://studentersamfundet.no/historie.php">Historie</a></li>
                            <li><a href="http://studentersamfundet.no/billetter.php">Billetter</a></li>
                            <li><a href="http://studentersamfundet.no/medlemmer.php">Bli medlem</a></li>
                            <li><a href="http://studentersamfundet.no/medlem/index.php">Registrere medlemskort</a></li>
                        </ul>
                    </div>
                </li>
                <li id="program-meny"><a href="http://studentersamfundet.no/prog.php">Program</a>
                    <div class="sub-menu">
                        <ul>
                            <li><a href="http://studentersamfundet.no/prog.php">Alle</a></li>
                            <li><a href="http://studentersamfundet.no/prog.php?type=konsert">Konsert</a></li>
                            <li><a href="http://studentersamfundet.no/prog.php?type=debatt">Debatt</a></li>
                            <li><a href="http://studentersamfundet.no/prog.php?type=film">Film</a></li>
                            <li><a href="http://studentersamfundet.no/prog.php?type=fest">Fest</a></li>
                            <li><a href="http://studentersamfundet.no/prog.php?type=teater">Teater</a></li>
                            <li><a href="http://studentersamfundet.no/prog.php?type=annet">Annet</a></li>
                            <li><a href="http://studentersamfundet.no/konsepter.php">Konsepter</a></li>
                            <li><a href="http://studentersamfundet.no/booking.php">Booking</a></li>
                        </ul>
                    </div>
                </li>
                <li id="foreninger-meny"><a href="http://studentersamfundet.no/foreninger.php">Foreninger</a>
                    <div class="sub-menu">
                        <ul>
                            <li><a href="http://studentersamfundet.no/foreninger.php">Foreninger</a></li>
                        </ul>
                    </div>
                </li>
                <li id="forum-meny"><a href="http://studentersamfundet.no/forum/index.php">Forum</a>
                    <div class="sub-menu">
                        <ul>
                            <li><a href="http://studentersamfundet.no/forum/viewforum.php?f=68">Musikk</a></li>
                            <li><a href="http://studentersamfundet.no/forum/viewforum.php?f=69">Debatt</a></li>
                            <li><a href="http://studentersamfundet.no/forum/viewforum.php?f=70">Fritt forum</a></li>
                        </ul>
                    </div>
                </li>
                <li id="inside-meny" class="current"><a href="https://www.studentersamfundet.no/inside">For medlemmer</a>
                    <div class="sub-menu">
                        <ul>
                            <li><a href="https://www.studentersamfundet.no/inside/">Inside</a></li>
                            <li><a href="http://studentersamfundet.no/viteboka/?page_id=3">Viteboka</a></li>
                            <li><a href="https://www.studentersamfundet.no/inside/index.php?page=display-barshifts-calendar">Tappet&aring;rnets vaktliste</a>
                            <li><a href="http://studentersamfundet.no/aktive.php">Andre ressurser</a></li>
                        </ul>
                    </div>
                </li>
                <li id="nyheter-meny"><a href="http://studentersamfundet.no/nyheter.php">Nyheter</a>
                    <div class="sub-menu">
                        <ul>
                        </ul>
                    </div>
                </li>
            </ul>
        </div> <!-- #menu -->
      </div> <!-- #header -->

      <div id="content-wrap">
<?php 
  include("menu.php");
?>
      <div id="content" class="clearfix">