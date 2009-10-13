<?php

class Navigation {

   public
   function display(){
?>
      <div id="nav"> 
  
        <a class="skip-nav" href="#content">hopp til innhold</a>
        <ul id="navList">
<?php 
   if(checkAuth("view-menu-quick")){
          ?>
          <li class="menulist">
            <a href="#" id="quickSwitch" class="switch">personlig</a>
            <ul id="quickMenu" class="menu">
              <li><a href="index.php?page=display-current-user">oppdatér brukerinformasjon</a></li>
              <li><a href="index.php?page=change-username">endre brukernavn</a></li>
            <?php if (!isMember(getCurrentUser())){?>
              <li><a href="index.php?page=register-membership">kjøp/registrér medlemskap</a></li>      
						<?php }else if (membershipExpired(getCurrentUser())){?>
      				<li><a href="index.php?page=register-membership">aktiver medlemskap</a></li>
      			<?php } ?>
            </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-messages")){
          ?>
          <li class="menulist">
            <a href="#" id="messagesSwitch" class="switch">meldinger</a>
            <ul id="messagesMenu" class="menu">
              <li><a href="index.php?page=display-messages">vis meldinger</a></li>
              <li><a href="index.php?page=display-bugreports">vis feilmeldinger</a></li>
              <li>ny melding</li>
            </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-jobs")){
          ?>
          <li class="menulist">
            <a href="#" id="jobsSwitch" class="switch">ledige stillinger</a>
            <ul id="jobsMenu" class="menu">
              <li><a href="index.php?page=display-jobs">vis ledige</a></li>
              <?php if(isAdmin()){
            ?><li><a href="index.php?page=register-job">registrér stilling</a></li>
<?php } 
?>              <li><a href="index.php?page=display-jobs-archive">arkiv</a></li>
            </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-barshifts")){
          ?>
          <li class="menulist">
            <a href="#" id="barshiftsSwitch" class="switch">tappetårnets vaktliste</a>
            <ul id="barshiftsMenu" class="menu">
              <li><a href="index.php?page=display-barshifts">vis kommende</a></li>
              <li><a href="index.php?page=display-barshifts-calendar">vis kalender</a></li>
              <?php if(checkAuth("view-register-barshift")){
            ?><li><a href="index.php?page=register-barshift">registrér vakt</a></li>
<?php } ?> 
           </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-events")){
          ?>
          <li class="menulist">
            <a href="#" id="eventsSwitch" class="switch">møter/kurs/internfester</a>
            <ul id="eventsMenu" class="menu">
              <li><a href="index.php?page=display-events-calendar">kalender</a></li>
              <li><a href="index.php?page=display-events">kommende aktiviteter</a></li>
              <?php if(checkAuth("view-register-event")){
            ?><li><a href="index.php?page=register-event">registrér aktivitet</a></li>
<?php } 
?>              <li><a href="index.php?page=display-all-calendar">kombolender</a></li>
            </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-concerts")){
          ?>
          <li class="menulist">
            <a href="#" id="concertsSwitch" class="switch">konserter/program</a>
            <ul id="concertsMenu" class="menu">
              <li><a href="index.php?page=display-concerts-calendar">kalender</a></li>
              <li><a href="index.php?page=display-concerts">kommende program</a></li>
              <?php if(checkAuth("view-register-concert")){
            ?><li><a href="index.php?page=register-concert">registrér arrangement</a></li>
<?php } 
?>          <?php if(checkAuth("view-week-program")){
            ?><li><a href="index.php?page=week-program">ukesprogram</a></li>
      				<li><a href="index.php?page=program-selection">programutvalg</a></li>
      <?php }?>          
            </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-divisions")){
          ?>
          <li class="menulist">
            <a href="#" id="divisionsSwitch" class="switch">foreninger</a>
            <ul id="divisionsMenu" class="menu">
              <li><a href="index.php?page=display-divisions">vis foreninger</a></li>
              <?php if(checkAuth("view-register-division")){
            ?><li><a href="index.php?page=register-division">registrér forening</a></li>
<?php } 
?>              <li><a href="index.php?page=display-positions">vis stillingsbeskrivelser</a></li>
              <?php if(checkAuth("view-register-position")){
            ?><li><a href="index.php?page=register-position">registrér stillingsbeskrivelse</a></li>
<?php } 
?>          </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-documents")){
          ?>
          <li class="menulist">
            <a href="#" id="documentsSwitch" class="switch">dokumenter</a>
            <ul id="documentsMenu" class="menu">
              <li><a href="index.php?page=display-documents">vis dokumenter</a></li>
              <?php if(checkAuth("view-upload-document")){
            ?><li><a href="index.php?page=upload-document">last opp nytt</a></li>
<?php } ?>            </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-users")){
          ?>
          <li class="menulist">
            <a href="#" id="usersSwitch" class="switch">medlemmer</a>
            <ul id="usersMenu" class="menu">
              <li><a href="index.php?page=display-users">vis medlemmer</a></li>
              <?php if(checkAuth("view-register-user")){
            ?><li><a href="index.php?page=register-user">registrér medlem</a></li>
<?php } 
?>            <?php if(checkAuth("view-display-user-expiries")){
            ?><li><a href="index.php?page=display-user-expiries">administrér utløpsdatoer</a></li>
<?php } 
?>            <?php if(checkAuth("view-register-membership-bankpayment")){
            ?><li><a href="index.php?page=register-membership-bankpayment">registrer bankbetalinger</a></li>
<?php } 
?>            <?php if(checkAuth("view-register-user-ea-update")){
            ?><li><a href="index.php?page=register-user-ea-update">registrer EA oppdatering</a></li>
<?php } 
?>            <?php if(checkAuth("view-display-division-requests")){
            ?><li><a href="index.php?page=display-division-requests">vis aktivforespørseler</a></li>
<?php } 
?>            <?php if(checkAuth("view-register-usergrouprelationship")){
            ?><li><a href="index.php?page=register-usergrouprelationship">registrér gruppemedlemskap</a></li>
<?php } 
?>            <?php if(checkAuth("view-display-usergrouprelationships")){
            ?><li><a href="index.php?page=display-usergrouprelationships">slett gruppemedlemskap</a></li>
<?php }
?>            <?php if(checkAuth("view-display-users-study-place")){
            ?><li><a href="index.php?page=display-users-study-place">vis studieplass-statistikk</a></li>
<?php }
?>            <?php if(checkAuth("view-payex-testpage")){
            ?><li><a href="index.php?page=payex-form">payex testside</a></li>
<?php } 
?>          </ul>
          </li>
      <?php }?>          
<?php 
   if(checkAuth("view-menu-access")){
          ?>
          <li class="menulist">
            <a href="#" id="accessSwitch" class="switch">administrering av tilgang</a>
            <ul id="accessMenu" class="menu">
              <li><a href="index.php?page=display-groups">vis grupper</a></li>
              <li><a href="index.php?page=register-group">registrér gruppe</a></li>
              <li><a href="index.php?page=display-actions">vis handlinger</a></li>
              <li><a href="index.php?page=register-action">registrér handling</a></li>
              <li><a href="index.php?page=display-actiongrouprelationships">vis forhold</a></li>
              <li><a href="index.php?page=register-actiongrouprelationship">registrér forhold</a></li>
            </ul>
          </li>
          <?php }?>    

<?php 
   if(checkAuth("view-menu-webpages")){
          ?>
          <li class="menulist">
            <a href="#" id="webpagesSwitch" class="switch">administrér nettsider</a>
            <ul id="webpagesMenu" class="menu">
              <li><a href="index.php?page=display-articles">vis nyheter</a></li>
              <li><a href="index.php?page=register-article">registrér nyhet</a></li>
            </ul>
          </li>
      <?php }?>          
<?php      
   if(checkAuth("view-menu-settings")){
          ?>
          <li class="menulist">
            <a href="#" id="settingsSwitch" class="switch">innstillinger</a>
            <ul id="settingsMenu" class="menu">
              <li><a href="index.php?action=switch-formtype&amp;section=settings">bytt skjemastil</a></li>
              <li><a href="index.php?action=switch-tinymce-theme&amp;section=settings">bytt RT-editor</a></li>
              <li><a href="index.php?page=register-documentcategory">dokumenttyper</a></li>
              <li><a href="index.php?page=register-eventcategory">aktitivitetstyper</a></li>
              <li><a href="index.php?page=register-jobcategory">stillingstyper</a></li>
              <li><a href="index.php?page=register-product">produkter for salg</a></li>
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
            <a href="#" id="samarbeidspartnereSwitch" class="switch">samarbeidspartnere</a>
            <ul id="samarbeidspartnereMenu" class="menu">
              <li><a href="#">rediger partnere</a></li>
              <li><a href="#">godkjenne plasser</a></li>
              <li><a href="#">vis oversikt</a></li>
              <li><a href="#">rediger e-post</a></li>
            </ul>
          </li>
<?php 
  }

  if(checkAuth("view-menu-webshop")){
          ?>
          <li class="menulist">
            <a href="#" id="webshopSwitch" class="switch">nettbutikk</a>
            <ul id="webshopMenu" class="menu">
              <li><a href="index.php?page=display-webshop">vis produkter</a></li>
              <li><a href="index.php?page=display-carts">vis handlekurver</a></li>              
            <?php if (checkAuth('view-display-sales')) {?>
              <li><a href="index.php?page=display-sales">vis solgte produkter</a></li>
            <?php } ?>              
            </ul>
          </li>
<?php 
  }

  if (loggedIn()){
?>          
          <li class="menulist">
            <a href="#" id="linksSwitch" class="switch">andre sider</a>
            <ul id="linksMenu" class="menu">
              <li><a href="http://www.studentersamfundet.no/index.php">studentersamfundet.no</a></li>
              <li><a href="http://www.studentersamfundet.no/forum/index.php">forum</a></li>
<?php
    if (isActive()) {
?>
              <li><a href="http://www.studentersamfundet.no/coppermine/index.php">bildegalleri</a></li>
              <li><a href="http://www.studentersamfundet.no/info/index.php">infovræl</a></li>
	          <li><a href="http://booking.studentersamfundet.no/">bookingsystemet</a></li>
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
?><a href="index.php?action=log-out">logg ut</a>
<?php 
   } 
?>
      </div>
<?php
  }
}
?>
