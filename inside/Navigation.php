<?php

class Navigation {

   public
   function display(){
?>
    <nav id="nav" class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
	    <!-- button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navList">
		<span class="sr-only">Skru av/på navigasjon</a>
		<span class="icon-bar">Icon bar</span>
		<span class="icon-bar">Icon bar</span>
	    </button -->
	    <a class="navbar-brand" href="http://inside.studentersamfundet.no/">Inside</a>
	</div>	


<ul id="navList" class="nav navbar-nav">

<?php
   if(checkAuth("view-menu-jobs")){
          ?>
          <!--<li class="menulist dropdown">
            <a href="#" id="jobsSwitch" class="switch dropdown-toggle">Ledige stillinger <b class="caret"></b></a>
            <ul id="jobsMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=display-jobs">Vis ledige</a></li>
              <?php if(isAdmin()){
            ?><li><a href="index.php?page=register-job">Registrér stilling</a></li>
<?php }
?>              <li><a href="index.php?page=display-jobs-archive">Arkiv</a></li>
            </ul>
          </li>-->
<?php }

   if(checkAuth("view-menu-divisions")) {
?>
          <li class="menulist dropdown">
            <a href="#" id="divisionsSwitch" class="switch dropdown-toggle">Foreninger <b class="caret"></b></a>
            <ul id="divisionsMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=display-divisions">Vis foreninger</a></li>
              <?php if(checkAuth("view-register-division")){
            ?><li><a href="index.php?page=register-division">Registrér forening</a></li>
<?php }
?>              <!-- <li><a href="index.php?page=display-positions">Vis stillingsbeskrivelser</a></li>-->
              <?php if(checkAuth("view-register-position")){
            ?><!--<li><a href="index.php?page=register-position">Registrér stillingsbeskrivelse</a></li>-->
<?php }
?>          </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-documents")){
          ?>
          <li class="menulist dropdown">
            <a href="#" id="documentsSwitch" class="switch dropdown-toggle">Dokumenter <b class="caret"></b></a>
            <ul id="documentsMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=display-documents">Vis dokumenter</a></li>
              <?php if(checkAuth("view-upload-document")){
            ?><li><a href="index.php?page=upload-document">Last opp nytt</a></li>
<?php } ?>            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-users")){
          ?>
          <li class="menulist dropdown">
            <a href="#" id="usersSwitch" class="switch dropdown-toggle">Medlemmer <b class="caret"></b></a>
            <ul id="usersMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=display-users">Vis medlemmer</a></li>
              <?php if(checkAuth("view-register-user")){
            ?><li><a href="index.php?page=register-user">Registrér medlem</a></li>
<?php }
?>            <?php if(checkAuth("view-display-user-expiries")){
            ?><!--<li><a href="index.php?page=display-user-expiries">Medlemskort: utløpsdatoer</a></li>-->
<?php }
?>            <?php if(checkAuth("view-register-membership-bankpayment")){
            ?><!--<li><a href="index.php?page=register-membership-bankpayment">Registrer bankbetalinger</a></li>-->
<?php }
?>            <?php if(checkAuth("view-register-user-ea-update")){
            ?><!--<li><a href="index.php?page=register-user-ea-update">Registrer EA oppdatering</a></li>-->
<?php }
?>            <?php if(checkAuth("view-display-division-requests")){
            ?><!--<li><a href="index.php?page=display-division-requests">Vis aktivforespørseler</a></li>-->
<?php }
?>            <?php if(checkAuth("view-register-usergrouprelationship")){
            ?><li><a href="index.php?page=register-usergrouprelationship">Registrér gruppemedlemskap</a></li>
<?php }
?>            <?php if(checkAuth("view-display-usergrouprelationships")){
            ?><li><a href="index.php?page=display-usergrouprelationships">Slett gruppemedlemskap</a></li>
<?php }
?>            <?php if(checkAuth("view-display-users-study-place")){
            ?><li><a href="index.php?page=display-users-study-place">Studieplass-statistikk</a></li>
<?php }
?>            <?php if(checkAuth("view-payex-testpage")){
            ?><!--<li><a href="index.php?page=payex-form">Payex testside</a></li>-->
<?php }
?>          </ul>
          </li>
      <?php }?>
<?php
      if(checkAuth("view-membership-sale")){
      ?>
          <!--<li class="menulist dropdown">
            <a href="#" id="billettbodSwitch" class="switch dropdown-toggle">Billettbod <b class="caret"></b></a>
            <ul id="billettbodMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=membership-sale">Salg av medlemskap</a></li>
              <?php if(checkAuth("view-membercard-production")){
              ?><li><a href="index.php?page=membercard-production">Administrer medlemskort</a></li>
<?php }
?>            </ul>
          </li>-->
      <?php }?>
<?php
   if(checkAuth("view-menu-access")){
          ?>
          <li class="menulist dropdown">
            <a href="#" id="accessSwitch" class="switch dropdown-toggle">Tilgang <b class="caret"></b></a>
            <ul id="accessMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=display-groups">Vis grupper</a></li>
              <li><a href="index.php?page=register-group">Registrér gruppe</a></li>
              <li><a href="index.php?page=display-actions">Vis handlinger</a></li>
              <li><a href="index.php?page=register-action">Registrér handling</a></li>
              <li><a href="index.php?page=display-actiongrouprelationships">Vis forhold</a></li>
              <li><a href="index.php?page=register-actiongrouprelationship">Registrér forhold</a></li>
            </ul>
          </li>
          <?php }

   if(checkAuth("view-menu-settings")){
          ?>
          <li class="menulist dropdown">
            <a href="#" id="settingsSwitch" class="switch dropdown-toggle">Innstillinger <b class="caret"></b></a>
            <ul id="settingsMenu" class="menu dropdown-menu">
              <!--<li><a href="index.php?action=switch-formtype&amp;section=settings">Bytt skjemastil</a></li>
              <li><a href="index.php?action=switch-tinymce-theme&amp;section=settings">Bytt RT-editor</a></li>-->
              <li><a href="index.php?page=register-documentcategory">Dokumenttyper</a></li>
              <!--<li><a href="index.php?page=register-eventcategory">Aktitivitetstyper</a></li>-->
              <!--<li><a href="index.php?page=register-jobcategory">Stillingstyper</a></li>-->
              <li><a href="index.php?page=register-product">Produkter for salg</a></li>
            </ul>
          </li>
<?php }

  /*if (isAdmin()) {
          ?>
          <li class="menulist dropdown">
            <a href="#" id="smsSwitch" class="switch dropdown-toggle">SMS (legacy) <b class="caret"></b></a>
            <ul id="smsMenu" class="menu dropdown-menu">
              <li><a href="?page=display-sms-log">Vis logg for SMS-tjeneste</a></li>
            </ul>
          </li>
<?php
  }*/

  if(checkAuth("view-menu-webshop")){
          ?>
          <li class="menulist dropdown">
            <a href="#" id="webshopSwitch" class="switch dropdown-toggle">Nettbutikk <b class="caret"></b></a>
            <ul id="webshopMenu" class="menu dropdown-menu">
              <li><a href="index.php?page=display-webshop">Vis produkter</a></li>
              <li><a href="index.php?page=display-carts">Vis handlekurver</a></li>
            <?php if (checkAuth('view-display-sales')) {?>
              <li><a href="index.php?page=display-sales">Vis solgte produkter</a></li>
            <?php } ?>
            </ul>
          </li>
<?php
  }
?>
        </ul>
      </nav>
<?php
  }
}
?>
