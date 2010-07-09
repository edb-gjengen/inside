<?php 
  // get access manager
  $manager = App_Base_ApplicationRegistry::getAccessManager();
?>

<div id="nav">

  <a class="skip-nav" href="#content">hopp til innhold</a>
    <ul id="navList">
<?php
  if ($manager->getCommandAccess("view-shifts")) {
?>
    <li class="menulist">
      <a href="#" id="quickSwitch" class="switch">vaktliste</a>
      <ul id="quickMenu" class="menu">
        <li><a href="<?=$_SERVER["PHP_SELF"]?>?cmd=view-shifts">vis mine vakter</a></li>
        <li><a href="<?=$_SERVER["PHP_SELF"]?>?cmd=view-calendar">vis kalender</a></li>
<?php 
    if ($manager->getCommandAccess("add-shift")) {
?>
        <li><a href="<?=$_SERVER["PHP_SELF"]?>?cmd=add-shift">legg til nye vakter</a></li>
<?php
    } 
?>
	  </ul>
    </li>
<?php 
  }
?>
	</ul>
<?php
   if ($manager->getCommandAccess("logout")) {
?>
<a href="<?=$_SERVER["PHP_SELF"]?>?cmd=logout">logg ut</a>
<?php
   }
?>
      </div>