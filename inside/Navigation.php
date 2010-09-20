<?php

class Navigation {

   public
   function display(){
?>
      <div id="nav">

        <a class="skip-nav" href="#content">Hopp til innholdet</a>
        <ul id="navList">
<?php
   if(checkAuth("view-menu-quick")){
          ?>
          <li class="menulist">
            <a href="#" id="quickSwitch" class="switch">Personlig</a>
            <ul id="quickMenu" class="menu">
              <li><a href="index.php?page=display-current-user">Oppdatér brukerinformasjon</a></li>
              <li><a href="index.php?page=change-username">Endre brukernavn</a></li>
            <?php if (!isMember(getCurrentUser())){?>
              <li><a href="index.php?page=register-membership">Kjøp/registrér medlemskap</a></li>
						<?php }else if (membershipExpired(getCurrentUser())){?>
      				<li><a href="index.php?page=register-membership">Aktiver medlemskap</a></li>
      			<?php } ?>
            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-messages")){
          ?>
          <li class="menulist">
            <a href="#" id="messagesSwitch" class="switch">Meldinger</a>
            <ul id="messagesMenu" class="menu">
              <li><a href="index.php?page=display-messages">Vis meldinger</a></li>
              <li><a href="index.php?page=display-bugreports">Vis feilmeldinger</a></li>
              <li>Ny melding</li>
            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-jobs")){
          ?>
          <li class="menulist">
            <a href="#" id="jobsSwitch" class="switch">Ledige stillinger</a>
            <ul id="jobsMenu" class="menu">
              <li><a href="index.php?page=display-jobs">Vis ledige</a></li>
              <?php if(isAdmin()){
            ?><li><a href="index.php?page=register-job">Registrér stilling</a></li>
<?php }
?>              <li><a href="index.php?page=display-jobs-archive">Arkiv</a></li>
            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-barshifts")){
          ?>
          <li class="menulist">
            <a href="#" id="barshiftsSwitch" class="switch">Tappetårnets vaktliste</a>
            <ul id="barshiftsMenu" class="menu">
              <li><a href="index.php?page=display-barshifts">Vis kommende</a></li>
              <li><a href="index.php?page=display-barshifts-calendar">Vis kalender</a></li>
              <?php if(checkAuth("view-register-barshift")){
            ?><li><a href="index.php?page=register-barshift">Registrér vakt</a></li>
<?php } ?>
           </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-events")){
          ?>
          <li class="menulist">
            <a href="#" id="eventsSwitch" class="switch">Møter/kurs/internfester</a>
            <ul id="eventsMenu" class="menu">
              <li><a href="index.php?page=display-events-calendar">Kalender</a></li>
              <li><a href="index.php?page=display-events">Kommende aktiviteter</a></li>
              <?php if(checkAuth("view-register-event")){
            ?><li><a href="index.php?page=register-event">Registrér aktivitet</a></li>
<?php }
?>              <li><a href="index.php?page=display-all-calendar">Kombolender</a></li>
            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-concerts")){
          ?>
          <li class="menulist">
            <a href="#" id="concertsSwitch" class="switch">Konserter/program</a>
            <ul id="concertsMenu" class="menu">
              <li><a href="index.php?page=display-concerts-calendar">Kalender</a></li>
              <li><a href="index.php?page=display-concerts">Kommende program</a></li>
              <?php if(checkAuth("view-register-concert")){
            ?><li><a href="index.php?page=register-concert">Registrér arrangement</a></li>
<?php }
?>          <?php if(checkAuth("view-week-program")){
            ?><li><a href="index.php?page=week-program">Ukesprogram</a></li>
      				<li><a href="index.php?page=program-selection">Programutvalg</a></li>
      <?php }?>
            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-divisions")){
          ?>
          <li class="menulist">
            <a href="#" id="divisionsSwitch" class="switch">Foreninger</a>
            <ul id="divisionsMenu" class="menu">
              <li><a href="index.php?page=display-divisions">Vis foreninger</a></li>
              <?php if(checkAuth("view-register-division")){
            ?><li><a href="index.php?page=register-division">Registrér forening</a></li>
<?php }
?>              <li><a href="index.php?page=display-positions">Vis stillingsbeskrivelser</a></li>
              <?php if(checkAuth("view-register-position")){
            ?><li><a href="index.php?page=register-position">Registrér stillingsbeskrivelse</a></li>
<?php }
?>          </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-documents")){
          ?>
          <li class="menulist">
            <a href="#" id="documentsSwitch" class="switch">Dokumenter</a>
            <ul id="documentsMenu" class="menu">
              <li><a href="index.php?page=display-documents">Vis dokumenter</a></li>
              <?php if(checkAuth("view-upload-document")){
            ?><li><a href="index.php?page=upload-document">Last opp nytt</a></li>
<?php } ?>            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-users")){
          ?>
          <li class="menulist">
            <a href="#" id="usersSwitch" class="switch">Medlemmer</a>
            <ul id="usersMenu" class="menu">
              <li><a href="index.php?page=display-users">Vis medlemmer</a></li>
              <?php if(checkAuth("view-register-user")){
            ?><li><a href="index.php?page=register-user">Registrér medlem</a></li>
<?php }
?>            <?php if(checkAuth("view-display-user-expiries")){
            ?><li><a href="index.php?page=display-user-expiries">Medlemskort: utløpsdatoer</a></li>
<?php }
?>            <?php if(checkAuth("view-register-membership-bankpayment")){
            ?><li><a href="index.php?page=register-membership-bankpayment">Registrer bankbetalinger</a></li>
<?php }
?>            <?php if(checkAuth("view-register-user-ea-update")){
            ?><li><a href="index.php?page=register-user-ea-update">Registrer EA oppdatering</a></li>
<?php }
?>            <?php if(checkAuth("view-display-division-requests")){
            ?><li><a href="index.php?page=display-division-requests">Vis aktivforespørseler</a></li>
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
            ?><li><a href="index.php?page=payex-form">Payex testside</a></li>
<?php }
?>          </ul>
          </li>
      <?php }?>
<?php
      if(checkAuth("view-membership-sale")){
      ?>
          <li class="menulist">
            <a href="#" id="billettbodSwitch" class="switch">Billettbod</a>
            <ul id="billettbodMenu" class="menu">
              <li><a href="index.php?page=membership-sale">Salg av medlemskap</a></li>
              <?php if(checkAuth("view-membercard-production")){
              ?><li><a href="index.php?page=membercard-production">Administrer medlemskort</a></li>
<?php }
?>            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-access")){
          ?>
          <li class="menulist">
            <a href="#" id="accessSwitch" class="switch">Tilgang</a>
            <ul id="accessMenu" class="menu">
              <li><a href="index.php?page=display-groups">Vis grupper</a></li>
              <li><a href="index.php?page=register-group">Registrér gruppe</a></li>
              <li><a href="index.php?page=display-actions">Vis handlinger</a></li>
              <li><a href="index.php?page=register-action">Registrér handling</a></li>
              <li><a href="index.php?page=display-actiongrouprelationships">Vis forhold</a></li>
              <li><a href="index.php?page=register-actiongrouprelationship">Registrér forhold</a></li>
            </ul>
          </li>
          <?php }?>

<?php
   if(checkAuth("view-menu-webpages")){
          ?>
          <li class="menulist">
            <a href="#" id="webpagesSwitch" class="switch">Nyheter</a>
            <ul id="webpagesMenu" class="menu">
              <li><a href="index.php?page=display-articles">Vis nyheter</a></li>
              <li><a href="index.php?page=register-article">Skriv nyhet</a></li>
            </ul>
          </li>
      <?php }?>
<?php
   if(checkAuth("view-menu-settings")){
          ?>
          <li class="menulist">
            <a href="#" id="settingsSwitch" class="switch">Innstillinger</a>
            <ul id="settingsMenu" class="menu">
              <li><a href="index.php?action=switch-formtype&amp;section=settings">Bytt skjemastil</a></li>
              <li><a href="index.php?action=switch-tinymce-theme&amp;section=settings">Bytt RT-editor</a></li>
              <li><a href="index.php?page=register-documentcategory">Dokumenttyper</a></li>
              <li><a href="index.php?page=register-eventcategory">Aktitivitetstyper</a></li>
              <li><a href="index.php?page=register-jobcategory">Stillingstyper</a></li>
              <li><a href="index.php?page=register-product">Produkter for salg</a></li>
            </ul>
          </li>
<?php }

  if (isAdmin()) {
          ?>
          <li class="menulist">
            <a href="#" id="smsSwitch" class="switch">SMS</a>
            <ul id="smsMenu" class="menu">
              <li><a href="?page=display-sms-log">Vis logg for SMS-tjeneste</a></li>
            </ul>
          </li>
<?php
  }

  if(isAdmin()){
          ?>
          <li class="menulist">
            <a href="#" id="samarbeidspartnereSwitch" class="switch">Samarbeidspartnere</a>
            <ul id="samarbeidspartnereMenu" class="menu">
              <li><a href="#">Rediger partnere</a></li>
              <li><a href="#">Godkjenne plasser</a></li>
              <li><a href="#">Vis oversikt</a></li>
              <li><a href="#">Rediger e-post</a></li>
            </ul>
          </li>
<?php
  }

  if(checkAuth("view-menu-webshop")){
          ?>
          <li class="menulist">
            <a href="#" id="webshopSwitch" class="switch">Nettbutikk</a>
            <ul id="webshopMenu" class="menu">
              <li><a href="index.php?page=display-webshop">Vis produkter</a></li>
              <li><a href="index.php?page=display-carts">Vis handlekurver</a></li>
            <?php if (checkAuth('view-display-sales')) {?>
              <li><a href="index.php?page=display-sales">Vis solgte produkter</a></li>
            <?php } ?>
            </ul>
          </li>
<?php
  }

  if (loggedIn()){
?>
          <li class="menulist">
            <a href="#" id="linksSwitch" class="switch">Andre sider</a>
            <ul id="linksMenu" class="menu">
              <li><a href="http://www.studentersamfundet.no/index.php">Studentersamfundet.no</a></li>
              <li><a href="http://www.studentersamfundet.no/forum/index.php">Forum</a></li>
<?php
    if (isActive()) {
?>
              <li><a href="http://www.studentersamfundet.no/coppermine/index.php">Bildegalleri</a></li>
              <li><a href="http://www.studentersamfundet.no/info/index.php">Infovræl</a></li>
	          <li><a href="http://booking.studentersamfundet.no/">Bookingsystemet</a></li>
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
   if (loggedIn()){
?><a href="index.php?action=log-out">Logg ut</a>
<?php
   }
?>
      </div>
<?php
  }
}
?>
