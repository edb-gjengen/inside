<?php

$beginHTML = false; //Set to true after preload errors and messages are displayed.

class Page {

  var $page_title;
  var $page;
  var $theme;
  var $section;

  function Page(){
    $this->__construct();
  }

  function __construct(){
    $this->page_title = "Studentersamfundet Inside";
    $this->page       = scriptParam("page");
    $this->section    = getSectionFromPage($this->page);//scriptParam("section");
    if (isset($_SESSION['theme'])){
      $this->theme = $_SESSION['theme'];
    }else {
      $this->theme = 'screen.css';
    }
  }

  public function display(){
    $this->_displayHeader();
    ?>
  <body class="dns section-inside">
    <div id="container">
      <div id="header">
        <span class="site-title"><a href="http://studentersamfundet.no/"><?php print $this->page_title; ?></a></span>
      </div> <!-- #header -->

      <div id="content-wrap">
      <div id="content" class="clearfix">
<?php
    $this->_displayNavigation();
?>
      <div id="inside-content-column">
<?php

    if ($GLOBALS['err_counter'] > 0){
      Errors::display();
    }
    if ($GLOBALS['msg_counter'] > 0){
      Messages::display();
    }
    if (isset($GLOBALS['extraScriptParams']['report-bug'])){
      displayBugReportForm($GLOBALS['extraScriptParams']['report-bug']);
    }

    $GLOBALS['beginHTML'] = true;


    if (!isset($_SESSION['valid-user'])){
      if ($this->page == 'register-user'){
        $this->_registerUser();
      }else {
        displayLogin();
      }
    }else {
    ?>
        <noscript>
          <div class="messages fancybox">
            <p>Disse sidene er optimalisert for bruk med JavaScript.</p>
            <p>Det anbefales derfor at du aktiviserer JavaScript.</p>
            <p>En del funksjoner vil ikke fungere som forventet uten JavaScript, men alt innhold skal likevel være tilgjengelig.</p>
           </div>
        </noscript>
<?php
    if ( getCurrentUser() ) {
	$user = new User( getCurrentUser() );
?>
	<div class="profile-info">
	    <span class="gravatar">
		<?php echo get_gravatar( $user->email , 20 , 'mm' , 'g' , true ); ?>
	    </span>
	    <span class="username">
		<a href="index.php?page=display-current-user"><?php echo( "$user->firstname $user->lastname" ); ?></a>
	    </span>
	    <span class="logout">
		(<a href="index.php?action=log-out">Logg ut</a>)
	    </span>
	</div>
<?php
    }

    if (checkAuth("view-" . $this->page) || checkResponsible()){

    //Determine what page to display
      switch ($this->page) {
        case "welcome":
        $this->_displayWelcome();
        break;

        case "home":
        $this->_displayHome();
        break;

        case "reset-password":
        $this->_resetPassword();
        break;

        case "display-article":
        $this->_displayArticle();
        break;

        case "display-articles":
        $this->_displayArticles();
        break;

        case "register-article":
        $this->_registerArticle();
        break;

        case "edit-article":
        $this->_editArticle();
        break;

        case "display-jobs":
        $this->_displayJobs("NOW()");
        break;

        case "display-jobs-archive":
        $this->_displayJobs("0000-00-00");
        break;

        case "display-job":
        $this->_displayJob();
        break;

        case "register-job":
        $this->_registerJob();
        break;

        case "edit-job":
        $this->_editJob();
        break;

        case "display-barshifts":
        $this->_displayBarShifts("NOW()");
        break;

        case "display-barshifts-calendar":
        $this->_displayBarShiftsCalendar();
        break;

        case "display-barshift":
        $this->_displayBarShift();
        break;

        case "register-barshift":
        $this->_registerBarShift();
        break;

        case "edit-barshift":
        $this->_editBarShift();
        break;

        case "display-divisions":
        $this->_displayDivisions();
        break;

        case "display-division":
        $this->_displayDivision();
        break;

        case "register-division":
        $this->_registerDivision();
        break;

        case "edit-division":
        $this->_editDivision();
        break;

        case "display-positions":
        $this->_displayPositions("NOW()");
        break;

        case "display-positions-archive":
        $this->_displayPositions("0000-00-00");
        break;

        case "display-position":
        $this->_displayPosition();
        break;

        case "register-position":
        $this->_registerPosition();
        break;

        case "edit-position":
        $this->_editPosition();
        break;

        case "display-events":
        $this->_displayEvents("NOW()");
        break;

        case "display-events-archive":
        $this->_displayEvents("0000-00-00");
        break;

        case "display-events-calendar":
        $this->_displayEventsCalendar();
        break;

        case "display-event":
        $this->_displayEvent();
        break;

        case "register-event":
        $this->_registerEvent();
        break;

        case "edit-event":
        $this->_editEvent();
        break;

        case "display-concerts":
        $this->_displayConcerts("NOW()");
        break;

        case "display-concerts-calendar":
        $this->_displayConcertsCalendar();
        break;

        case "display-concert":
        $this->_displayConcert();
        break;

        case "register-concert":
        $this->_registerConcert();
        break;

        case "edit-concert":
        $this->_editConcert();
        break;

        case "edit-concertreport":
        $this->_editConcertReport();
        break;

        case "display-all-calendar":
        $this->_displayAllCalendar();
        break;

        case "week-program":
        $this->_displayWeekProgram();
        break;

        case "program-selection":
        $this->_displayProgramSelection();
        break;

        case "edit-program-selection":
        $this->_editProgramSelection();
        break;

        case "display-documents":
        $this->_displayDocuments();
        break;

        case "upload-document":
        $this->_uploadDocument();
        break;

        case "edit-document":
        $this->_editDocument();
        break;

        case "display-groups":
        $this->_displayGroups();
        break;

        case "register-group":
        $this->_registerGroup();
        break;

        case "display-group":
        case "edit-group":
        $this->_editGroup();
        break;

        case "display-actions":
        $this->_displayActions();
        break;

        case "register-action":
        $this->_registerAction();
        break;

        case "display-action":
        case "edit-action":
        $this->_editAction();
        break;

        case "display-actiongrouprelationships":
        $this->_displayActionGroupRelationships();
        break;

        case "register-actiongrouprelationship":
        $this->_registerActionGroupRelationship();
        break;

        case "display-division-requests":
        $this->_displayDivisionRequests();
        break;

        case "display-usergrouprelationships":
        $this->_displayUserGroupRelationships();
        break;

        case "register-usergrouprelationship":
        $this->_registerUserGroupRelationship();
        break;

        case "display-users":
        $this->_displayUsers();
        break;

        case "display-user-expiries":
        $this->_displayUserExpiries();
        break;

        case "display-users-study-place":
          $this->_displayUsersStudyPlace();
          break;

        case "register-user-ea-update":
          $this->_registerUserEAUpdate();
          break;

        case "register-membership-bankpayment":
          $this->_registerUserMembershipBankpayment();
          break;

        case "register-user":
        $this->_registerUser();
        break;

        case "display-current-user":
        $this->_editUser(getCurrentUser());
        break;

        case "display-user":
        case "edit-user":
        $this->_editUser();
        break;

        case "change-username":
        $this->_changeUsername();
        break;

        case "register-membership":
        $this->_registerMembership();
        break;

        case "renew-membership":
        $this->_renewMembership();
        break;

        case "register-membership-payex-confirm":
        $this->_registerMembershipPayexConfirm();
        break;

        case "payex-form":
        $this->_payexForm();
        break;

        case "register-documentcategory":
        $this->_registerDocumentCategory();
        break;

        case "edit-documentcategory":
        $this->_editDocumentCategory();
        break;

        case "register-eventcategory":
        $this->_registerEventCategory();
        break;

        case "edit-eventcategory":
        $this->_editEventCategory();
        break;

        case "register-jobcategory":
        $this->_registerJobCategory();
        break;

        case "edit-jobcategory":
        $this->_editJobCategory();
        break;

	case "display-product":
	$this->_displayProduct();
	break;

	case "display-products":
        case "register-product":
        $this->_registerProduct();
        break;

        case "edit-product":
        $this->_editProduct();
        break;

        case "display-bugreport":
        $this->_displayBugReport();
        break;

        case "display-bugreports":
        $this->_displayBugReports();
        break;

	case "display-webshop":
	$this->_displayWebshop();
	break;

	case "display-sales":
	$this->_displaySales();
	break;

	case "display-sales-item":
	$this->_displaySalesItem();
	break;

	case "display-cart":
	$this->_displayCart();
	break;

	case "display-carts":
	$this->_displayCarts();
	break;

	case "cart-checkout":
	$this->_cartCheckout();
	break;

	case "transaction-confirmation":
	$this->_transactionConfirmation();
	break;

	case "display-sms-log":
	$this->_displaySmsLog();
	  break;

	case 'membership-sale':
	  $this->_membershipSale();
	  break;
	case 'membercard-production':
	  $this->_membercardProduction();
	  break;

        default:
        $this->_displayHome();
      }

    } else {
      $this->_displayAccessDenied();
    }
  }
?>
      </div>

    </div> <!-- #content-wrap -->
  </div> <!-- #inside-content-column -->

    <!-- Google Analytics -->
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-52914-11']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>

  </body>
</html>
    <?php
  }

  public function _displayHeader(){?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">

  <head>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <link rel="stylesheet" title="default" type="text/css"
          href="../css/<?php print $this->theme; ?>" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="../css/style.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="../css/handheld.css" media="handheld" />
    <link rel="stylesheet" type="text/css" href="../css/print.css" media="print" />
<?php if (scriptParam("debug")) { ?>
    <link rel="stylesheet" type="text/css" href="../css/misund.css" media="screen" />
<?php } ?>
    <link rel="shortcut icon" href="favicon.ico" />
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/functions.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/ajax.js"></script>
    <script type="text/javascript" src="<?php print $GLOBALS['include_path'];?>zXml/zxml.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/finder.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/sorttable.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/menuExpandable3.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/XMLRequest.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/XMLZipRequest.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/XMLCatRequest.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/XMLUsernameRequest.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php print $GLOBALS['include_path'];?>tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript">
      tinyMCE.init({
      editor_selector: "mceEditor",
      mode : "textareas",
      valid_elements: "a[href],p,strong/b,em/i,ul,ol,li,br",
      theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,link,unlink,code,undo,redo",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      safari_warning: false,
      relative_urls : false,
      convert_urls : false
      });
    </script>
    <script type="text/javascript">
        window.onload = function() {
            initializeMenu("quickMenu", "quickSwitch");
            initializeMenu("messagesMenu", "messagesSwitch");
            initializeMenu("webpagesMenu", "webpagesSwitch");
            initializeMenu("jobsMenu", "jobsSwitch");
            initializeMenu("barshiftsMenu", "barshiftsSwitch");
            initializeMenu("eventsMenu", "eventsSwitch");
            initializeMenu("concertsMenu", "concertsSwitch");
            initializeMenu("divisionsMenu", "divisionsSwitch");
            initializeMenu("documentsMenu", "documentsSwitch");
            initializeMenu("usersMenu", "usersSwitch");
            initializeMenu("billettbodMenu", "billettbodSwitch");
            initializeMenu("accessMenu", "accessSwitch");
            initializeMenu("settingsMenu", "settingsSwitch");
            initializeMenu("samarbeidspartnereMenu", "samarbeidspartnereSwitch"); // Test av samarbeidspartnere
            initializeMenu("smsMenu", "smsSwitch");
            initializeMenu("webshopMenu", "webshopSwitch");
            initializeMenu("linksMenu", "linksSwitch");

        		ul2finder();
	    			cssjs('add', document.body, 'jsenabled');
       <?php
            if ($this->page == "display-user" || $this->page == "register-user" ||
                $this->page == "display-current-user"){
            ?>
            initializeUsersForm();
              <?php
            }
            if ($this->page == "register-job" && scriptParam("positionid") == null){
              print ("initializeJobForm('multilist');");
            }?>
            menuExpandCurrentSection("<?php print($this->section."Menu"); ?>");
        }
    </script>
    <!-- Migration (nikolark) -->
        <link type="text/css" href="<?php echo $GLOBALS['static_path'];?>css/migration/smoothness/jquery-ui-1.8.14.custom.css" rel="stylesheet" />	
        <link type="text/css" href="<?php echo $GLOBALS['static_path'];?>css/migration/nikolark.css" rel="stylesheet" />
        <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/migration/jquery-1.5.1.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/migration/jquery-ui-1.8.14.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/migration/jquery.validate.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/migration/custom_validators.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['static_path'];?>js/migration/byebye_inside.js"></script>
    <title><?php print $this->page_title; ?></title>
  </head>


<?php
  }

  public function _displayNavigation(){
    $nav = new Navigation();
    $nav->display();
  }

  public
  function _displayWelcome(){
    print "<p>Velkommen til Studentersamfundets intranett.</p>\n";
  }

  public
  function _displayHome(){
    $user = new User(getCurrentUser());
?>
  <div class="text-column">

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3>Velkommen til Studentersamfundets medlemssider!</h3>
      </div>
      <div class="panel-body">
        <p>Du er logget inn som <strong><?php print getCurrentUserName(); ?></strong>.</p>
      </div>
    </div>

    <?=$user->membershipStatus()?>

<?php
	if (!membershipNextYear(getCurrentUser())) {
		?>
		<h3>Kjøp medlemskap for neste år allerede nå!</h3>
		<p><a href="index.php?page=register-membership">Kjøp direkte med kredittkort eller registrér medlemskap kjøpt i bar nå!</a></p>
		<?php
	}

  if (!isActive()){?>
    	<?php
    if (!isMember()){?>
			<a class="btn btn-primary" href="index.php?page=register-membership">Registrér eller kjøp medlemskap</a>
			<?php
    }else if (membershipExpired(getCurrentUser())){?>
			<a class="btn btn-primary" href="index.php?page=register-membership">Registrér eller kjøp medlemskap</a>
			<?php
    }
		?>
			<a class="btn" href="index.php?page=display-current-user">Oppdatér brukerinfo</a>
		<?php
  } else {

    if (isBoardMember() || isAdmin()){
      //Meldinger til styremedlemmer kan skrives her
    }
    ?>
      <?php if (!isMember(getCurrentUser())){?>
      <h3>Du har ikke registrert medlemskort</h3>
      <p>Om du har kjøpt medlemskap må du <a href="index.php?page=register-membership">registrere kortnummer og kode</a>.</p>
      <?php if (checkAuth("view-register-membership-payex")) ?><p>Du kan også <a href="index.php?page=register-membership">kjøpe medlemskap med VISA-kort</a>.</p> <?php ; ?>
<?php }else if (membershipExpired(getCurrentUser())){?>
      <h3>Medlemskapet ditt er utgått!</h3>
      <p>Om du har kjøpt medlemskap i en av barene på huset må du <a href="index.php?page=renew-membership">aktivere medlemskapet ditt med aktiveringsnummer og aktiveringskode</a>!</p>
      <?php if (checkAuth("view-register-membership-payex")) ?><p>Du kan også <a href="index.php?page=register-membership">kjøpe medlemskap med VISA-kort</a>.</p> <?php ; ?>
<?php }


    ?>
    <div class="often-used-links">
        <h3>Hva vil du gjøre?</h3>
      <div class="list-group">
        <a class="list-item" href="index.php?page=display-webshop">Besøke nettbutikken</a>
      <?php
      if (checkAuth('view-display-division-requests')){ ?>
        <a class="list-item" href="index.php?page=display-division-requests">Godkjenn aktivforespørseler</a>
      <?php } ?>
        <a class="list-item" href="index.php?page=display-jobs">Se på ledige stillinger</a>
          (<?php print getUnreadCount('job', 'expires'); ?> nye ikke utgåtte)
        <!--<li><a href="index.php?page=display-events-calendar">Se på aktivitetskalenderen</a>
          (<?php print getUnreadCount('event', 'time'); ?> nye kommende)</li>-->
        <a class="list-item" href="index.php?page=display-documents">Lese dokumenter</a>
        <a class="list-item" href="index.php?page=display-divisions">Finne kontaktinfo til en forening</a>
        <a class="list-item" href="index.php?page=display-current-user">Oppdatere min brukerinformasjon</a>
      </div>
    </div>
<?php }

if (isAdmin()) {
?>
	<div class="panel">
	  <div class="panel-heading">
            <h3>For administrator</h3>
          </div>
          <div class="panel-body">
            <a href="index.php?page=display-bugreports">Vis feilmeldinger</a>
            (<?php print getBugreportCount(); ?> ubehandlede)</li>
<?php }

if (isActive()) {
  	// Brukersøk
    	$title   = "Søk etter bruker";
    	$id      = "usersearch";
    	$enctype = NULL;
    	$method  = "post";
    	$action  = "index.php?page=display-users";
    	$fields  = Array();

    	$fields[] = Array("label" => "Søk etter medlem", "type" => "text",
      	                "attributes" => Array("name" => "search"));
    	$form = new Form($title, $enctype, $method, $action, $fields, $id);
    	$form->display("horizontal");
}
        // Migration LDAP (nikolark)
        $uid = getCurrentUser();
        if( !is_migrated($uid) ) {
?>
                <div id="infomodal">
                <div class="ui-widget"> 
                <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;"> 
                  <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span> 
                  <strong>DNS oppdaterer EDB-systemet!</strong><br />Vi (EDB) har ryddet opp på kottet i Slemdalsveien 15 og har funnet internsystemet <span style="font-style:italic;">Inside</span> modent for søppeldynga.</p>
                </div> 
                </div>
                Vi ber derfor alle medlemmer <span style="font-weight:bold;">bekrefte sitt brukernavn og passord</span> slik at vi kan pensjonere dette systemet og tilby nye og bedre tjenester. Dette medfører at noen av dere må bytte brukernavn (de med spesialtegn, mellomrom og lignende).
                <h3>Hvorfor?</h3>
                <ul>
                <li><span style="font-weight:bold;">Medlem:</span> For å få tilgang til trådløst nettverk.</li>
                <li><span style="font-weight:bold;">Aktiv:</span> For å få tilgang til maskiner.</li>
                </ul>
                </div>
                <script type="text/javascript">
                /* open the migrate dialog */
                $( document ).ready( function() {
                    $("#infomodal").dialog('open');
                });
                </script>
<?php
        }
?>
  </div>

<?php
  }

   public function _displayAccessDenied(){?>
  <div class="text-column">
    <p>Du har ikke tilgang til å se denne siden. Om du tror du skulle hatt tilgang, vennligst kontakt <a href="mailto:support@studentersamfundet.no">support</a>.</p>

    <p>Det kan også hende at siden du forsøkte å besøke ikke er publisert ennå.</p>
  </div>

<?php
  }

  public function _resetPassword(){
    print("<p>Passordet ditt er nettopp blitt resatt. Bruk skjemaet under til å endre passordet til noe som er lettere å huske.</p>");
    $title   = "endre passord";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-password";
    $fields  = Array();

    $fields[] =Array("label" => "nytt passord", "type" => "password",
                     "attributes" => Array("name" => "password1"));
    $fields[] =Array("label" => "gjenta passord", "type" => "password",
                     "attributes" => Array("name" => "password2"));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

  }

  public function _displayJobs($selection = "0000-00-00"){
    $jobs = new Jobs();
    $jobs->displayList($selection);
  }

  public function _displayJob(){
    $jobId = scriptParam("jobid");
    $job = new Job($jobId);
    $job->display();
  }

  public function _registerJob(){
    $posId = scriptParam("positionid");
    if (!empty($posId)){
      $pos = new Position($posId);
    }

    $positions = new Positions();
    $posList   = $positions->getList();

    $jobCat = new JobCategories();
    $categories = $jobCat->getCategories();

    $user        = new User(getCurrentUser());
    $contactInfo = $user->getContactInfo();

    print("<p><strong>Merk:</strong> stillinger som ikke er basert på stillingsbeskrivelser vil ikke vises på /jobb-sidene.</p>");

    $title   = "registrér stillingsutlysning";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-job&amp;page=display-job";
    $fields  = array();

    if (!isset($pos)){
      $fields[] = Array("label" => "positionid", "type" => "hidden",
                        "attributes" => Array("name" => "positionid"));
      $fields[] = Array("label" => "eksisterende stilling?", "type" => "radio",
                        "attributes" => Array("name" => "linkedToPos", "values" => Array("job-true", "job-false"),
                                              "labels" => Array("ja", "nei"),
                                              "value" => "job-false"));
      $fields[] = Array("label" => "&rarr;", "type" => "multilist",
                        "attributes" => Array("name" => "positionList", "disabled" => true,
                                              "values" => $posList));
      $comment = NULL;
    }else {
      $fields[] = Array("label" => "positionid", "type" => "hidden",
                        "attributes" => Array("name" => "positionid", "value" => "$posId"));

      $comment ="<h3>$pos->name i $pos->division_name</h3>\n
Teksten under er hentet fra kunnskapsdatabasen. Du står fritt til å endre den etter eget ønske. Endringer som gjøres i kunnskapsdatabasen etter denne utlysningen er registrert vil ikke gjenspeiles her. Stillingen er imidlertid knyttet opp mot stillingsbeskrivelsen, og vil bli annosert sammen med denne også.";
    }
    $fields[] = Array("label" => "tittel", "type" => "text",
                      "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                            "value" => (isset($pos->name)) ? $pos->name : ""));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "cols" => 70, "rows" => 12, "comment" => $comment,
                                            "class" => "mceEditor",
                                            "value" => (isset($pos->text)) ? $pos->text : ""));
    $fields[] = Array("label" => "utgår", "type" => "datetime",
                      "attributes" => Array("name" => "expires"));
    $fields[] = Array("label" => "kategori", "type" => "select",
                           "attributes" => Array("name" => "jobcategory_id", "values" => $categories));
    $fields[] = Array("label" => "kontaktinfo", "type" => "textarea",
                      "attributes" => Array("name" => "contactInfo",
                                            "comment" => "Epostadresser blir automatisk gjort om til linker ved visning.",
                                            "cols" => 70, "rows" => 4,
                                            "value" => $contactInfo));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editJob(){
    $jobCat = new JobCategories();
    $categories = $jobCat->getCategories();

    $jobId = scriptParam("jobid");
    $job   = new Job($jobId);
    $title   = "redigér stillingsutlysning";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-job&amp;page=display-job";
    $fields  = Array(Array("label" => "jobid", "type" => "hidden",
                           "attributes" => Array("name" => "jobid", "value" => "$jobId")),
                     Array("label" => "stilling", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $job->name)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 15,
                                                 "class" => "mceEditor",
                                                 "value" => $job->text)),
                     Array("label" => "utgår", "type" => "datetime",
                           "attributes" => Array("name" => "expires",
                                                 "value" => substr($job->expires, 0, 16))),
                     Array("label" => "kategori", "type" => "select",
                           "attributes" => Array("name" => "jobcategory_id", "values" => $categories,
                                                 "currentValue" => $job->jobcategory_id)),
                     Array("label" => "kontaktinfo", "type" => "textarea",
                           "attributes" => Array("name" => "contactInfo", "cols" => 70, "rows" => 4,
                                                 "value" => $job->contactInfo))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayBarShifts($selection = "0000-00-00"){
    $barshifts = new BarShifts();
    $barshifts->displayList($selection);
  }

  public function _displayBarshiftsCalendar(){
    $calendar = new Calendar("barshifts");
    $month = scriptParam("month");
    if (!empty($month)){
      $year  = substr($month, 0, 4);
      $month = substr($month, 4, 2);
    }else {
      $year  = date("Y");
      $month = date("m");
    }
    $calendar->display($year, $month);
  }

  public function _displayBarShift(){
    $barshiftId = scriptParam("barshiftid");
    $barshift = new BarShift($barshiftId);
    $barshift->display();
  }

  public function _registerBarShift(){

		$locs = new Locations();
		$locations = $locs->getList();


    $title   = "registrregistrérr barvakt";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-barshift&amp;page=display-barshift";
    $fields  = array();

    $fields[] = Array("label" => "tittel", "type" => "text",
                      "attributes" => Array("name" => "title" ,"size" => 50, "maxlength" => 255));
    $fields[] = Array("label" => "sted", "type" => "select",
                           "attributes" => Array("name" => "location_id", "values" => $locations));
    $fields[] = Array("label" => "dato", "type" => "date",
                      "attributes" => Array("name" => "date"));
    $fields[] = Array("label" => "start", "type" => "time",
                      "attributes" => Array("name" => "start"));
    $fields[] = Array("label" => "slutt", "type" => "time",
                      "attributes" => Array("name" => "end"));
    $fields[] = Array("label" => "antall folk", "type" => "text",
                      "attributes" => Array("name" => "num_workers" ,"size" => 3, "maxlength" => 3));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editBarShift(){
		$locs = new Locations();
		$locations = $locs->getList();

    $barshiftid = scriptParam("barshiftid");
    $barshift   = new BarShift($barshiftid);
    $title   = "redigér barvakt";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-barshift&amp;page=display-barshift";
    $fields  = Array();

    $fields[] = Array("label" => "barshiftid", "type" => "hidden",
                           "attributes" => Array("name" => "barshiftid", "value" => $barshiftid));
    $fields[] = Array("label" => "tittel", "type" => "text",
                           "attributes" => Array("name" => "title" ,"size" => 50, "maxlength" => 255,
                                                 "value" => $barshift->title));
    $fields[] = Array("label" => "sted", "type" => "select",
                           "attributes" => Array("name" => "location_id", "values" => $locations,
                                                 "currentValue" => $barshift->location_id));
    $fields[] = Array("label" => "dato", "type" => "date",
                           "attributes" => Array("name" => "date",
                                                 "value" => $barshift->date));
    $fields[] = Array("label" => "start", "type" => "time",
                           "attributes" => Array("name" => "start",
                                                 "value" => formatTime($barshift->start)));
    $fields[] = Array("label" => "slutt", "type" => "time",
                           "attributes" => Array("name" => "end",
                                                 "value" => formatTime($barshift->end)));
    $fields[] = Array("label" => "antall folk", "type" => "text",
                           "attributes" => Array("name" => "num_workers" ,"size" => 3, "maxlength" => 3,
                                                 "value" => $barshift->num_workers));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayDivisions(){
    $divisions = new Divisions();
    $divisions->displayList();
  }

  public function _displayDivision(){
    $divisionId = scriptParam("divisionid");
    $division = new Division($divisionId);
    if ($division->name != NULL){
      $division->display();
    }else {
      error(DIVISION . ": no division found");
    }
  }

  public function _registerDivision(){
    $divCatList = new DivisionCategories();
    $divCats    = $divCatList->getCategories();
    $userList = new Users();
    $users    = $userList->getList();

    $title   = "registrér ny forening eller nytt utvalg";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-division&amp;page=display-divisions";
    $fields  = Array(Array("label" => "foreningsnavn", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50)),
                     Array("label" => "type", "type" => "select",
                           "attributes" => Array("name" => "divisioncategory_id", "values" => $divCats)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 10,
                                                 "class" => "mceEditor")),
                     Array("label" => "telefon", "type" => "text",
                           "attributes" => Array("name" => "phone", "size" => 8, "maxlength" => 8)),
                     Array("label" => "epost", "type" => "text",
                           "attributes" => Array("name" => "email", "size" => 50, "maxlength" => 120)),
                     Array("label" => "hjemmeside", "type" => "text",
                           "attributes" => Array("name" => "url", "size" => 50, "maxlength" => 120)),
                     Array("label" => "kontor", "type" => "text",
                           "attributes" => Array("name" => "office", "size" => 3, "maxlength" => 3)),
                     Array("label" => "kontaktperson", "type" => "select",
                           "attributes" => Array("name" => "user_id_contact", "values" => $users))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editDivision(){
    $divCatList = new DivisionCategories();
    $divCats    = $divCatList->getCategories();
    $userList = new Users();
    $users    = $userList->getList();

    $divId   = scriptParam("divisionid");

    if(isset($_POST['isupdated']))
{
	$db = db_connect();
	if(!is_numeric($divId)) die();
	$db->query('UPDATE `din_division` SET `updated`=now() WHERE `id`=' . $divId );
	?>
Takk for at du oppdaterte siden!

<br /><br /><a href="index.php?page=display-division&divisionid=<?php echo $divId ?>">Til foreningsside</a>
<?php
return;	
}

    $div     = new Division($divId);
    $title   = "endre forening eller utvalg";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=update-division&amp;page=display-division";


    $fields  = Array();

    $fields[] = Array("label" => "divisionid", "type" => "hidden",
                      "attributes" => Array("name" => "divisionid", "value" => "$divId"));
   $fields[] = Array("label" => "foreningsnavn", "type" => "text",
                     "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $div->name));
   $fields[] = Array("label" => "type", "type" => "select",
                     "attributes" => Array("name" => "divisioncategory_id", "values" => $divCats,
                                           "currentValue" => $div->divisioncategory_id));

   $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 15,
                                                 "class" => "mceEditor",
                                                 "value" => $div->text));
   $fields[] = Array("label" => "telefon", "type" => "text",
                           "attributes" => Array("name" => "phone", "size" => 8, "maxlength" => 8,
                                                 "value" => $div->phone));
   $fields[] = Array("label" => "epost", "type" => "text",
                           "attributes" => Array("name" => "email", "size" => 50, "maxlength" => 120,
                                                 "value" => $div->email));
   $fields[] = Array("label" => "hjemmeside", "type" => "text",
                           "attributes" => Array("name" => "url", "size" => 50, "maxlength" => 120,
                                                 "value" => $div->url));
   $fields[] = Array("label" => "kontor", "type" => "text",
                           "attributes" => Array("name" => "office", "size" => 3, "maxlength" => 3,
                                                 "value" => $div->office));
   $fields[] = Array("label" => "kontaktperson", "type" => "select",
                     "attributes" => Array("name" => "user_id_contact", "values" => $users,
                                           "currentValue" => $div->user_id_contact));
    $fields[]  =Array("label" => "bilde", "type" => "file",
                      "attributes" => Array("name" => "userfile", "size" => 55));


    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

if($div->updated == null){

echo '<p>Denne siden kan det v&aelig;re veldig lenge siden ble oppdatert. Er informasjonen bra?';
?>
<form method="post">
<input name="isupdated" type="submit" value="Denne informasjonen er oppdatert og fin!" />
</form></p>
<?php

}else if(strtotime($div->updated) + 60 * 60 * 24 * 90  < time()):
echo '<p>Denne siden ble sist oppdatert ' . $div->updated . ', trenger den oppdatering?';
?>
<form method="post">
<input name="isupdated" type="submit" value="Nei, denne informasjonen er oppdatert og fin!" />
</form></p>
<?php
else:
echo '<p>Denne siden ble sist oppdatert ' . $div->updated . '</p>';
endif;
  }

  public function _displayPositions($selection = "0000-00-00"){
    $division = scriptParam("divisionid");
    $divList  = new Divisions();
    $divs    = $divList->getListAllWithPositions();

    $current = scriptParam("divisionid");

    $title   = "vis stillingsbeskrivelser";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-positions";
    $fields  = Array();

    $fields[] = Array("label" => "Vis stillingsbeskrivelser fra ", "type" => "select",
                      "attributes" => Array("name" => "divisionid", "values" => $divs,
                                            "currentValue" => $current));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");

    if (!empty($current)){
      $positions = new Positions();
      $positions->displayList($current);
    }
  }

  public function _displayPosition(){
    $positionId = scriptParam("positionid");
    $position = new Position($positionId);
    $position->display();
  }

  public function _displayUsersStudyPlace() {
    $users = new Users();
    $users->displayUsersStudyPlaceList();
  }

  public function _registerPosition(){
    $divList   = new Divisions();
    $divisions = $divList->getList();

    $title   = "registrér stillingsbeskrivelse";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-position&amp;page=display-positions";
    $fields  = Array(Array("label" => "stilling", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 10,
                                                 "class" => "mceEditor")),
                     Array("label" => "foreningstilknytning", "type" => "select",
                           "attributes" => Array("name" => "division_id", "values" => $divisions))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editPosition(){
    $divList   = new Divisions();
    $divisions = $divList->getList();

    $positionId = scriptParam("positionid");
    $position   = new Position($positionId);
    $title   = "oppdatér stillingsbeskrivlse";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-position&amp;page=display-position";
    $fields  = Array(Array("label" => "positionid", "type" => "hidden",
                           "attributes" => Array("name" => "positionid", "value" => "$positionId")),
                     Array("label" => "stilling", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $position->name)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 10,
                                                 "class" => "mceEditor",
                                                 "value" => $position->text)),
                     Array("label" => "foreningstilknytnig", "type" => "select",
                           "attributes" => Array("name" => "division_id", "values" => $divisions,
                                                 "currentValue" => $position->division_id))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayEvents($selection = "0000-00-00"){
    $events = new Events();
    $events->displayList($selection);
  }

  public function _displayEventsCalendar(){
    $calendar = new Calendar("events");
    $month = scriptParam("month");
    if (!empty($month)){
      $year  = substr($month, 0, 4);
      $month = substr($month, 4, 2);
    }else {
      $year  = date("Y");
      $month = date("m");
    }
    $calendar->display($year, $month);
  }

  public function _displayEvent(){
    $eventId = scriptParam("eventid");
    $event = new Event($eventId);
    $event->display();
  }

  public function _registerEvent(){
    $eventCat = new EventCategories();
    $categories = $eventCat->getCategories();
    $userList = new Users();
    $users    = $userList->getList();

    $title   = "registrér aktivitet";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-event&amp;page=display-event";
    $fields  = Array(Array("label" => "tittel", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 7,
                                                                            "class" => "mceEditor",
)),
                     Array("label" => "dato og tid", "type" => "datetime",
                           "attributes" => Array("name" => "time")),
                     Array("label" => "sted", "type" => "text",
                           "attributes" => Array("name" => "location" ,"size" => 50, "maxlength" => 50)),
                     Array("label" => "deltakere", "type" => "textarea",
                           "attributes" => Array("name" => "targetGroup", "cols" => 70, "rows" => 2,
                                                 "comment" => "Hvem kan/bør delta?", "maxlength" => 255)),
                     Array("label" => "kategori", "type" => "select",
                           "attributes" => Array("name" => "eventcategory_id", "values" => $categories)),
                     Array("label" => "ansvarlig", "type" => "select",
                           "attributes" => Array("name" => "user_id_responsible", "values" => $users,
                           "currentValue" => getCurrentUser()))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editEvent(){
    $eventCat = new EventCategories();
    $categories = $eventCat->getCategories();
    $userList = new Users();
    $users    = $userList->getList();

    $eventId = scriptParam("eventid");
    $event   = new Event($eventId);
    $title   = "oppdatér aktivitet";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-event&amp;page=display-event";
    $fields  = Array(Array("label" => "eventid", "type" => "hidden",
                           "attributes" => Array("name" => "eventid", "value" => "$eventId")),
                     Array("label" => "tittel", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $event->name)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 7,
                                                 "class" => "mceEditor",
                                                 "value" => $event->text)),
                     Array("label" => "dato og tid", "type" => "datetime",
                           "attributes" => Array("name" => "time",
                                                 "value" => $event->time)),
                     Array("label" => "sted", "type" => "text",
                           "attributes" => Array("name" => "location" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $event->location)),
                     Array("label" => "deltakere", "type" => "textarea",
                           "attributes" => Array("name" => "targetGroup", "cols" => 70, "rows" => 2,
                                                 "value" => $event->targetGroup,
                                                 "comment" => "Hvem kan/bør delta?", "maxlength" => 255)),
                     Array("label" => "kategori", "type" => "select",
                           "attributes" => Array("name" => "eventcategory_id", "values" => $categories,
                                                 "currentValue" => $event->eventcategory_id)),
                     Array("label" => "ansvarlig", "type" => "select",
                           "attributes" => Array("name" => "user_id_responsible", "values" => $users,
                                                 "currentValue" => $event->user_id_responsible))

                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayConcerts($selection = "0000-00-00"){
    $concerts = new Concerts();
    $concerts->displayList($selection);
  }

  public function _displayConcertsCalendar(){
    $calendar = new Calendar("concerts");
    $month = scriptParam("month");
    if (!empty($month)){
      $year  = substr($month, 0, 4);
      $month = substr($month, 4, 2);
    }else {
      $year  = date("Y");
      $month = date("m");
    }
    $calendar->display($year, $month);
  }

  public function _displayConcert(){
    $concertId = scriptParam("concertid");
    $concert = new Concert($concertId);
    $concert->display();
  }

  public function _registerConcert(){
    $concertCat = new ConcertCategories();
    $categories = $concertCat->getCategories();
    $venueList  = new Locations();
    $venues     = $venueList->getList();
    $divList    = new Divisions();
    $divs       = $divList->getList();
    $userList   = new Users();
    $users      = $userList->getList(2, -1);

    $title   = "Registrér arrangement";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=register-concert&amp;page=display-concert";
    $fields  = Array();

    $fields[] = Array("label" => "tittel", "type" => "text",
                      "attributes" => Array("name" => "name" ,"size" => 55,
	    	      "maxlength" => 50, "class" => "title", "value" => scriptParam("name")));
    $fields[] = Array("label" => "engelsk tittel", "type" => "text",
                      "attributes" => Array("name" => "name_en" ,"size" => 55,
		      "maxlength" => 50, "class" => "title", "value" => scriptParam("name_en")));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("comment" =>
                                            "Bruk link-knappen for å legge inn linker eller epostadresser.",
                                            "name" => "text", "cols" => 70, "rows" => 15,
                                            "class" => "mceEditor", "value" => scriptParam("text")));
    $fields[] = Array("label" => "engelsk beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text_en", "cols" => 70, "rows" => 15,
                      "class" => "mceEditor", "value" => scriptParam("text_en")));
    $fields[] = Array("label" => "sammendrag", "type" => "textarea",
                      "attributes" => Array("name" => "intro", "cols" => 70, "rows" => 3,
                                            "maxlength" => 250, "value" => scriptParam("intro")));
    $fields[] = Array("label" => "engelsk sammendrag", "type" => "textarea",
                      "attributes" => Array("name" => "intro_en", "cols" => 70, "rows" => 3,
                                            "maxlength" => 250, "value" => scriptParam("intro_en")));
    $fields[] = Array("label" => "dato og tid", "type" => "datetime",
                      "attributes" => Array("name" => "time", "value" => scriptParam("time")));
    $fields[] = Array("label" => "arrangør", "type" => "select",
                      "attributes" => Array("name" => "host_id", "values" => $divs, "currentValue" => scriptParam("host_id")));
    $fields[] = Array("label" => "sted", "type" => "select",
                      "attributes" => Array("name" => "venue_id", "values" => $venues, "currentValue" => scriptParam("venue_id")));
    $fields[] = Array("label" => "type", "type" => "select",
                      "attributes" => Array("name" => "concertcategory_id", "values" => $categories, "currentValue" => scriptParam("concertcategory_id")));
    $fields[] = Array("label" => "billettpris", "type" => "text",
                      "attributes" => Array("name" => "priceNormal" ,"size" => 4, "maxlength" => 5, "value" => scriptParam("priceNormal")));
    $fields[] = Array("label" => "medlemspris", "type" => "text",
                      "attributes" => Array("name" => "priceConcession" ,"size" => 4, "maxlength" => 5, "value" => scriptParam("priceConcession")));
    $fields[] = Array("label" => "linker", "type" => "textarea",
                      "attributes" => Array("comment" =>
                                            "én link per linje. Linker kan også inkluderes direkte i teksten over.",
                                            "name" => "links", "cols" => 70, "rows" => 3, "value" => scriptParam("links")));
    $fields[]  =Array("label" => "bilde", "type" => "file",
                      "attributes" => Array("comment" => "Vi blir glade hvis du bruker litt store bilder. 1024 piksler på den lengste kanten er kjempefint.",
		                            "name" => "userfile", "size" => 55, "value" => scriptParam("userfile")));
    $fields[] = Array("label" => "billettservice-link", "type" => "text",
                      "attributes" => Array("name" => "ticketLink" ,"size" => 50, "maxlength" => 255, "value" => scriptParam("ticketLink")));
    $fields[] = Array("label" => "facebook-link", "type" => "text",
                      "attributes" => Array("name" => "facebookLink" ,"size" => 50, "maxlength" => 60, "value" => scriptParam("facebookLink")));
    $fields[] = Array("label" => "ekstern kontakt", "type" => "text",
                      "attributes" => Array("name" => "user_name_ext_contact" ,"size" => 50, "maxlength" => 50, "value" => scriptParam("user_name_ext_contact"),
                      											"help" => "Ekstern kontakt er en person det kan være nyttig å kontakte i forbindelse med for eksempel markedsføring. Dette kan være manager, forlag, plateselskap osv. Disse feltene kan godt stå tomme."));
    $fields[] = Array("label" => "&rarr; telefon", "type" => "text",
                      "attributes" => Array("name" => "user_phone_ext_contact" ,"size" => 10, "maxlength" => 12, "value" => scriptParam("user_phone_ext_contact")));
    $fields[] = Array("label" => "&rarr; epost", "type" => "text",
                      "attributes" => Array("name" => "user_email_ext_contact" ,"size" => 50, "maxlength" => 120, "value" => scriptParam("user_email_ext_contact")));
    $fields[] = Array("label" => "&rarr; rolle", "type" => "text",
                      "attributes" => Array("name" => "user_role_ext_contact" ,"size" => 50, "maxlength" => 120, "value" => scriptParam("user_role_ext_contact")));
    $fields[] = Array("label" => "kommentar", "type" => "textarea",
                      "attributes" => Array("comment" =>
                                            "Kommentarer er til internt bruk, og vil ikke vises på hovedsidene.",
                                            "name" => "comment", "cols" => 70, "rows" => 3, "value" => scriptParam("comment")));

    print("<h3>HUSK: Fyll ut både tittel, sammendrag og tekst!</h3>");
#    print("<p>Studentersamfundet.no vil snarlig legge om alle sine visningsmaler. I de nye malene vil det være viktig å ha fylt ut alle disse 3 feltene.</p>");
?>
<div style="background-color:rgb(241,255,115); border:1px solid black; padding:5px; margin:5px;">
Om dere lager en facebook side til arrangementet, ikke glem &aring; putte inn lenka <a href="#facebookLink" onclick="document.getElementById(\"facebookLink\").focus();">her</a>! Hvis du ikke gj&oslash;r det, kan det hende KAK ogs&aring; lager en event for deg og da blir det dobbelt opp som ikke er bra... Keep up the good work :D
</div>
<?php

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editConcert(){
    $concertCat = new ConcertCategories();
    $categories = $concertCat->getCategories();
    $venueList  = new Locations();
    $venues     = $venueList->getList();
    $divList    = new Divisions();
    $divs       = $divList->getList();
    $userList   = new Users();
    $users      = $userList->getList();

    $concertId = scriptParam("concertid");
    $concert   = new Concert($concertId);

    $title   = "oppdatér arrangement";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=update-concert&amp;page=display-concert";
    $fields  = Array();

    $fields[] = Array("label" => "concertid", "type" => "hidden",
                            "attributes" => Array("name" => "concertid", "value" => "$concertId"));
    $fields[] = Array("label" => "tittel", "type" => "text",
                      "attributes" => Array("name" => "name" ,"size" => 55, "maxlength" => 50,
                                            "class" => "title", "value" => $concert->name));
    $fields[] = Array("label" => "engelsk tittel", "type" => "text",
                      "attributes" => Array("name" => "name_en" ,"size" => 55, "maxlength" => 50,
                                            "class" => "title", "value" => $concert->name_en));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("comment" =>
                                            "Bruk link-knappen for å legge inn linker eller epostadresser.<br />",
                                            "name" => "text", "cols" => 70, "rows" => 15,
                                            "class" => "mceEditor",
                                            "value" => stripSlashes($concert->text)));
    $fields[] = Array("label" => "engelsk beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text_en", "cols" => 70, "rows" => 15,
                                            "class" => "mceEditor",
                                            "value" => stripSlashes($concert->text_en)));
    $fields[] = Array("label" => "sammendrag", "type" => "textarea",
                      "attributes" => Array("name" => "intro", "cols" => 70, "rows" => 3,
                                            "maxlength" => 250,
                                            "value" => $concert->intro));
    $fields[] = Array("label" => "engelsk sammendrag", "type" => "textarea",
                      "attributes" => Array("name" => "intro_en", "cols" => 70, "rows" => 3,
                                            "maxlength" => 255,
                                            "value" => $concert->intro_en));
    $fields[] = Array("label" => "dato og tid", "type" => "datetime",
                      "attributes" => Array("name" => "time",
                                            "value" => $concert->time));
    $fields[] = Array("label" => "arrangør", "type" => "select",
                      "attributes" => Array("name" => "host_id", "values" => $divs,
                                            "currentValue" => $concert->host_id));
    $fields[] = Array("label" => "sted", "type" => "select",
                      "attributes" => Array("name" => "venue_id", "values" => $venues,
                                            "currentValue" => $concert->venue_id));
    $fields[] = Array("label" => "type", "type" => "select",
                      "attributes" => Array("name" => "concertcategory_id", "values" => $categories,
                                            "currentValue" => $concert->concertcategory_id));
    $fields[] = Array("label" => "billettpris", "type" => "text",
                      "attributes" => Array("name" => "priceNormal" ,"size" => 4, "maxlength" => 5,
                                            "value" => $concert->priceNormal));
    $fields[] = Array("label" => "medlemspris", "type" => "text",
                      "attributes" => Array("name" => "priceConcession" ,"size" => 4, "maxlength" => 5,
                                            "value" => $concert->priceConcession));
    $fields[]  =Array("label" => "bilde", "type" => "file",
                      "attributes" => Array("comment" => '<img src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/program/'.$concert->picture.'&amp;maxwidth=200\" alt="pressebilde" />Vi blir glade hvis du bruker litt store bilder. 1024 piksler på den lengste kanten er kjempefint.',
                      "name" => "userfile", "size" => 55));
    $fields[] = Array("label" => "linker", "type" => "textarea",
                      "attributes" => Array("comment" =>
                                            "én link per linje. Linker kan også inkluderes direkte i teksten over.",
                                            "name" => "links", "cols" => 70, "rows" => 3,
                                            "value" => $concert->getLinks("text")));
    $fields[] = Array("label" => "billettservice-link", "type" => "text",
                      "attributes" => Array("name" => "ticketLink" ,"size" => 50, "maxlength" => 255,
                                            "value" => $concert->ticketLink));
    $fields[] = Array("label" => "facebook-link", "type" => "text",
                      "attributes" => Array("name" => "facebookLink" ,"size" => 50, "maxlength" => 60,
                                            "value" => $concert->facebookLink));
    $fields[] = Array("label" => "ekstern kontakt", "type" => "text",
                      "attributes" => Array("name" => "user_name_ext_contact" ,"size" => 50, "maxlength" => 50,
                                            "value" => $concert->user_name_ext_contact,
                      											"help" => "Ekstern kontakt er en person det kan være nyttig å kontakte i forbindelse med for eksempel markedsføring. Dette kan være manager, forlag, plateselskap osv. Disse feltene kan godt stå tomme."));
    $fields[] = Array("label" => "&rarr; telefon", "type" => "text",
                      "attributes" => Array("name" => "user_phone_ext_contact" ,"size" => 10, "maxlength" => 12,
                                            "value" => $concert->user_phone_ext_contact));
    $fields[] = Array("label" => "&rarr; epost", "type" => "text",
                      "attributes" => Array("name" => "user_email_ext_contact" ,"size" => 50, "maxlength" => 120,
                                            "value" => $concert->user_email_ext_contact));
    $fields[] = Array("label" => "&rarr; rolle", "type" => "text",
                      "attributes" => Array("name" => "user_role_ext_contact" ,"size" => 50, "maxlength" => 120,
                                            "value" => $concert->user_role_ext_contact));
    $fields[] = Array("label" => "kommentar", "type" => "textarea",
                      "attributes" => Array("comment" =>
                                            "Kommentarer er til internt bruk, og vil ikke vises på hovedsidene.",
                                            "name" => "comment", "cols" => 70, "rows" => 3,
                                            "value" => $concert->comment));
    $fields[] = Array("label" => "viewWeekprogram", "type" => "hidden",
                            "attributes" => Array("name" => "viewWeekprogram",
                                                  "value" => $concert->viewWeekprogram));

    /*
    print("<h2>Ingress blir sammendrag</h2>");
    print("<p>Ingresser utgår fra visningsmalene. I stedet kan dere nå lagre sammendrag av artiklene, som vil vises på sider med flere artikler eller arrangementer.</p>");
     */

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

	public
	function _editConcertReport() {
		$report = new ConcertReport(scriptParam("concertreportid"));

		$title   = "registrér arrangementsrapport";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-concertreport&amp;page=display-concert&amp;concertid=$report->concert_id";
    $fields  = Array();

    $fields[] = Array("label" => "concertreportid", "type" => "hidden",
                           "attributes" => Array("name" => "concertreportid", "value" => $report->id));

  	$fields[] = Array("label" => "besøkende", "type" => "text",
                      "attributes" => Array("name" => "visitors","size" => 4,
                                            "maxlength" => 4,
                                            "value" => $report->visitors));
  	$fields[] = Array("label" => "resultat", "type" => "text",
                      "attributes" => Array("name" => "result","size" => 7,
                                            "maxlength" => 8,
                                            "value" => $report->result));
	 	$fields[] = Array("label" => "markedsføring", "type" => "textarea",
                      "attributes" => Array("name" => "marketing_comment", "cols" => 70,
                      											"rows" => 15, "class" => "mceEditor",
                      											"value" => $report->marketing_comment));

		$fields[] = Array("label" => "produksjon", "type" => "textarea",
                      "attributes" => Array("name" => "production_comment", "cols" => 70,
                      											"rows" => 15, "class" => "mceEditor",
                      											"value" => $report->production_comment));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
	}

  public function _displayAllCalendar(){
    $calendar = new Calendar("all");
    $month = scriptParam("month");
    if (!empty($month)){
      $year  = substr($month, 0, 4);
      $month = substr($month, 4, 2);
    }else {
      $year  = date("Y");
      $month = date("m");
    }
    $calendar->display($year, $month);
  }

  public function _displayWeekProgram(){
    $title   = "ny uke";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?section=concerts&page=week-program&action=register-weekprogram";
    $fields  = Array();

    $fields[] = Array("label" => "år", "type" => "text",
                      "attributes" => Array("name" => "year", "size" => 4, "maxlength" => 4, "value" => date("Y") ));

    $fields[] = Array("label" => "uke", "type" => "text",
                      "attributes" => Array("name" => "week", "size" => 2, "maxlength" => 2, "value" => date("W") ));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

    $list = new WeekPrograms();
    $list->displayList();

  }

  public function _displayProgramSelection(){
    $title   = "registrer programutvalg";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?section=concerts&page=program-selection&action=register-programselection";
    $fields  = Array();

    $fields[] = Array("label" => "fortekst", "type" => "textarea",
                      "attributes" => Array("name" => "pretext", "cols" => 70, "rows" => 10,
                                            "class" => "mceEditor"));
    $fields[] = Array("label" => "start", "type" => "date",
                      "attributes" => Array("name" => "start", "value" => date("Y-m-d") ));
    $fields[] = Array("label" => "slutt", "type" => "date",
                      "attributes" => Array("name" => "end", "value" => date("Y-m-d") ));
    $fields[] = Array("label" => "type", "type" => "text",
                      "attributes" => Array("name" => "type" ,"size" => 12, "maxlength" => 24,
                                            "value" => "Konsert", "tag" => "Skriv 'Billett' for å vise alle med billett"));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

    $list = new ProgramsSelection();
    $list->displayList();

	}

  public function _editProgramSelection(){
    $progid = scriptParam("programselectionid");
    $prog = new ProgramSelection($progid);

    $title   = "endre programutvalg";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?section=concerts&page=program-selection&action=update-programselection";
    $fields  = Array();

    $fields[] = Array("label" => "programselectionid", "type" => "hidden",
                            "attributes" => Array("name" => "programselectionid", "value" => $progid));
    $fields[] = Array("label" => "fortekst", "type" => "textarea",
                      "attributes" => Array("name" => "pretext", "cols" => 70, "rows" => 10,
                                            "class" => "mceEditor", "value" => $prog->pretext));
    $fields[] = Array("label" => "start", "type" => "date",
                      "attributes" => Array("name" => "start", "value" => date("Y-m-d"),
                      											"value" => $prog->start));
    $fields[] = Array("label" => "slutt", "type" => "date",
                      "attributes" => Array("name" => "end", "value" => date("Y-m-d"),
                      											"value" => $prog->end));
    $fields[] = Array("label" => "type", "type" => "text",
                      "attributes" => Array("name" => "type" ,"size" => 12, "maxlength" => 24,
                                            "value" => $prog->type));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
	}

  public function _displayDocuments(){
    $doccatlist  = new DocumentCategories();
    $doccats    = $doccatlist->getList();

    $current    = scriptParam("documentcategoryid");
    $currentTag = scriptParam("documenttag");

    $title   = "vis dokumenter";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?section=documents&page=display-documents";
    $fields  = Array();

    $fields[] = Array("label" => "Vis dokumenter av typen ", "type" => "select",
                      "attributes" => Array("name" => "documentcategoryid", "values" => $doccats,
                                            "currentValue" => $current));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");

    $title   = "søk på tagger";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?section=documents&page=display-documents";
    $fields  = Array();

    $fields[] = Array("label" => "Vis dokumenter merket ", "type" => "text",
                      "attributes" => Array("name" => "documenttag",
                                            "value" => $currentTag));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");

    if (!empty($currentTag)){
      $documents = new Documents();
      $documents->displayListTag($currentTag);
    }
    else if ($current >= 0){
      $documents = new Documents();
      $documents->displayList($current);
    }

  }


  public function _displayDocument(){
    $documentId = scriptParam("documentid");
    $document = new Document($documentId);
    $document->display();
  }

  public function _uploadDocument(){
    $documentCat = new DocumentCategories();
    $categories = $documentCat->getList();

    $title   = "last opp dokument";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=upload-document&amp;page=display-documents";
    $fields  = Array();
    $fields[] = Array("label" => "dokument", "type" => "file",
                           "attributes" => Array("name" => "file", "size" => 55));
    $fields[] = Array("label" => "kategori", "type" => "select",
                      "attributes" => Array("name" => "documentcategoryid",
                      "values" => $categories));
    $fields[] = Array("label" => "tags", "type" => "text",
                      "attributes" => Array("name" => "tags" ,"size" => 50, "maxlength" => 255));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editDocument(){
    $documentCat = new DocumentCategories();
    $categories = $documentCat->getList();

    $documentId = scriptParam("documentid");
    $document   = new Document($documentId);
    $title   = "oppdatér dokumentinfo";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-document&amp;page=display-documents";
    $fields   = Array();

    $fields[] = Array("label" => "documentid", "type" => "hidden",
                      "attributes" => Array("name" => "documentid", "value" => "$documentId"));
    $fields[] = Array("label" => "tittel", "type" => "text",
                      "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                      "value" => get_file_basename($document->name),
                      "tag" => get_file_suffix($document->name)));
    $fields[] = Array("label" => "kategori", "type" => "select",
                      "attributes" => Array("name" => "documentcategory_id",
                      "values" => $categories,
                      "currentValue" => $document->documentcategory_id));
    $fields[] = Array("label" => "tags", "type" => "text",
                      "attributes" => Array("name" => "tags" ,"size" => 50, "maxlength" => 255,
                      "value" => $document->tags));


    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayGroups(){
    $groups = new Groups();
    $groups->displayList();
  }

  public function _displayGroup(){
    $groupid = scriptParam("groupid");
    $group = new Group($groupid);
    $group->display();
  }

  public function _registerGroup(){
    $divs = new Divisions();
    $divList = $divs->getList();

    $title   = "registrér gruppe";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-group&amp;page=display-groups";
    $fields  = Array();

    $fields[] = Array("label" => "gruppenavn", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 3));
    $fields[] = Array("label" => "epostliste", "type" => "text",
                           "attributes" => Array("name" => "mailinglist" ,"size" => 50, "maxlength" => 50));
    $fields[] = Array("label" => "forening", "type" => "select",
                           "attributes" => Array("name" => "division_id",
                                                        "values" => $divList, "nullPossible" => true));
    $fields[] = Array("label" => "foreningsmoderator", "type" => "radio",
                      "attributes" => Array("name" => "admin", "values" => Array(1, 0),
                                            "labels" => Array("ja", "nei"),
                                            "value" => "nei",
                                            "comment" => "Foreningsmoderator kan endre og slette alle elementer som er tilknyttet foreningen."));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editGroup(){
    $groupid = scriptParam("groupid");
    $group = new Group($groupid);
    $exclude = Array();
    $exclude[] = ($groupid == 1) ? 0 : 1;//If not group "alle", hide actions from "alle"
		$exclude[] = ($groupid > 2) ? 2 : NULL; //If not group "aktive", hide acions from "aktive"
		if ($group->isAdmin() == true) { //if group is related to "foreningsstyre", hide "foreningsstyre"
			$exclude[] = 61;
		}

    $divs = new Divisions();
    $divList = $divs->getList();
    $rels = new ActionGroupRelationships();
    $list = $rels->getJoinedList($groupid, "array", $exclude);

    $title   = "oppdatér gruppe";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-group&amp;page=display-group";
    $fields  = Array();

    $fields[] = Array("label" => "groupid", "type" => "hidden",
                           "attributes" => Array("name" => "groupid", "value" => "$groupid"));
    $fields[] = Array("label" => "gruppenavn", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $group->name));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 7,
                                                 "value" => $group->text));
    $fields[] = Array("label" => "epostliste", "type" => "text",
                           "attributes" => Array("name" => "mailinglist" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $group->mailinglist));
    $fields[] = Array("label" => "forening", "type" => "select",
                           "attributes" => Array("name" => "division_id", "currentValue" => $group->division_id,
                                                        "values" => $divList, "nullPossible" => true));
    $fields[] = Array("label" => "foreningsmoderator", "type" => "radio",
                           "attributes" => Array("name" => "admin", "values" => Array(1, 0),
                                                 "labels" => Array("ja", "nei"),
                                                 "value" => $group->admin,
                                                 "comment" => "Foreningsmoderator kan endre og slette alle elementer som er tilknyttet foreningen."));
    $fields[] = Array("label" => "tilgang", "type" => "checkbox-list",
                           "attributes" => Array("name" => "actions",
																								 "options" => $list,
																								 "comment" => "Viser kun handlinger som ikke gruppen får gjennom andre grupper den automatisk er medlem av."));


    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayActions(){
    $actions = new Actions();
    $actions->displayList();
  }

  public function _displayAction(){
    $actionid = scriptParam("actionid");
    $action = new Action($actionid);
    $action->display();
  }

  public function _registerAction(){

    $title   = "registrér handling";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-action&amp;page=display-actions";
    $fields  = Array(Array("label" => "handling", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 3))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editAction(){
    $actionid = scriptParam("actionid");
    $action1 = new Action($actionid);

    $title   = "oppdatér handling";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-action&amp;page=display-action";
    $fields  = Array(Array("label" => "actionid", "type" => "hidden",
                           "attributes" => Array("name" => "actionid", "value" => "$actionid")),
                     Array("label" => "handling", "type" => "text",
                           "attributes" => Array("name" => "name" ,"size" => 50, "maxlength" => 50,
                                                 "value" => $action1->name)),
                     Array("label" => "beskrivelse", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 7,
                                                 "value" => $action1->text))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayActionGroupRelationships(){
    $relations = new ActionGroupRelationships();
    $relations->displayList();
  }

  public function _registerActionGroupRelationship(){
    $actionList = new Actions();
    $actions    = $actionList->getList();
    $groupList  = new Groups();
    $groups     = $groupList->getList();


    $title   = "knytt handling til gruppe ";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-actiongrouprelationship&amp;page=register-actiongrouprelationship";
    $fields  = Array(Array("label" => "handling", "type" => "select",
                           "attributes" => Array("name" => "actionid", "values" => $actions)),
                     Array("label" => "gruppe", "type" => "select",
                           "attributes" => Array("name" => "groupid", "values" => $groups))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayArticles(){
    $articles = new Articles();
    $articles->displayList();
  }

  public function _displayArticle(){
    $articleid = scriptParam("articleid");
    $article = new Article($articleid);
    $article->display();
  }

  public function _registerArticle(){
    $title   = "registrér nyhet";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=register-article&amp;page=display-article";
    $fields  = Array();

    $fields[] = Array("label" => "title", "type" => "text",
                           "attributes" => Array("name" => "title", "class" => "title", "size" => 50, "maxlength" => 50));
    $fields[] = Array("label" => "tekst", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 10,
                           "class" => "mceEditor"));
    $fields[] = Array("label" => "sammendrag", "type" => "textarea",
                           "attributes" => Array("name" => "intro", "cols" => 70, "rows" => 3, "maxlength" => 250));
    /**
     * Hvis value ikke er gitt, blir dagens dato satt.
     * Hvor? Aner ikke!
     * --Thomas Misund Hansen, 2009-06-09
     */
    $fields[] = Array("label" => "utgår", "type" => "date",
                           "attributes" => Array("name" => "expires", "value" => "2029-01-01"));
    $fields[]  =Array("label" => "hovedbilde", "type" => "file",
                      "attributes" => Array("comment" => "Vi blir glade hvis du bruker litt store bilder. 1024 piksler på den lengste kanten er kjempefint.",
                                            "name" => "attachment1", "size" => 55));
    $fields[] = Array("label" => "billedtekst", "type" => "textarea",
                           "attributes" => Array("name" => "caption1", "cols" => 70, "rows" => 2, "maxlength" => 120));
    $fields[]  =Array("label" => "ekstra bilde", "type" => "file",
                      "attributes" => Array("name" => "attachment2", "size" => 55));
    $fields[] = Array("label" => "billedtekst", "type" => "textarea",
                           "attributes" => Array("name" => "caption2", "cols" => 70, "rows" => 2, "maxlength" => 120));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editArticle(){
    $articleid = scriptParam("articleid");
    $article = new Article($articleid);

    $title   = "endre nyhet";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=update-article&amp;page=display-article";
    $fields  = Array();

    $fields[] = Array("label" => "articleid", "type" => "hidden",
                           "attributes" => Array("name" => "articleid", "value" => $articleid));
    $fields[] = Array("label" => "title", "type" => "text",
                           "attributes" => Array("name" => "title", "class" => "title", "size" => 50, "maxlength" => 50,
                                                 "value" => $article->title));
    $fields[] = Array("label" => "tekst", "type" => "textarea",
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 10,
                                                 "class" => "mceEditor",
    						 "value" => $article->text));
    $fields[] = Array("label" => "sammendrag", "type" => "textarea",
                           "attributes" => Array("name" => "intro", "cols" => 70, "rows" => 3, "maxlength" => 250,
                           "value" => $article->intro));
    $fields[] = Array("label" => "utgår", "type" => "date",
                           "attributes" => Array("name" => "expires", "value" => $article->expires));
    $fields[]  =Array("label" => "hovedbilde", "type" => "file",
                      "attributes" => Array("comment" => "Vi blir glade hvis du bruker litt store bilder. 1024 piksler på den lengste kanten er kjempefint.",
                                            "name" => "attachment1", "size" => 55));
    $fields[] = Array("label" => "billedtekst", "type" => "textarea",
                           "attributes" => Array("name" => "caption1", "cols" => 70, "rows" => 2, "maxlength" => 120,
                                                 "value" => $article->caption1));
    $fields[]  =Array("label" => "ekstra bilde", "type" => "file",
                      "attributes" => Array("name" => "attachment2", "size" => 55));
    $fields[] = Array("label" => "billedtekst", "type" => "textarea",
                           "attributes" => Array("name" => "caption2", "cols" => 70, "rows" => 2, "maxlength" => 120,
                                                 "value" => $article->caption2));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _displayDivisionRequests(){
    $users = new Users();
    $users->displayDivisionRequests();
  }

  public function _displayUsergroupRelationships(){
    $groupList  = new Groups("admin-only");
    $groups     = $groupList->getList();
    $limits = Array(Array("id" => 15, "title" => 15),
                    Array("id" => 25, "title" => 25),
                    Array("id" => 50, "title" => 50),
                    Array("id" => 100, "title" => 100),
                    Array("id" => 10000, "title" => "alle")
                    );

    $current = scriptParam("groupid");
    if (empty($current)){
      $current = 2;
    }

    $limit = scriptParam("limit");
    if (empty($limit)){
      $limit = 25;
    }

    $title   = "vis gruppemedlemmer";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-usergrouprelationships";
    $fields  = Array(Array("label" => "vis", "type" => "select",
                           "attributes" => Array("name" => "limit", "values" => $limits, "currentValue" => $limit)),
                     Array("label" => "fra", "type" => "select",
                           "attributes" => Array("name" => "groupid", "values" => $groups, "currentValue" => $current))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");

    $relations = new UserGroupRelationships();
    $relations->displayList($current, $limit);
  }

  public function _registerUserGroupRelationship(){
    $userlist  = new Users();
    $users     = $userlist->getList(-2, 0);

    $groups = Array(Array("id" => 2, "title" => "Aktiv"));

    $title   = "registrÃ©r medlem som aktiv";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-usergrouprelationship&amp;page=register-usergrouprelationship";
    $fields  = Array(Array("label" => "medlem", "type" => "select",
                           "attributes" => Array("name" => "userid", "values" => $users)),
                     Array("label" => "gruppe", "type" => "select",
                           "attributes" => Array("name" => "groupid", "values" => $groups))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

    $userlist    = new Users();
    $users       = $userlist->getList(2);
    $groupList   = new Groups("admin-only");
    $groups      = $groupList->getList();
    $currentUser = scriptParam("userid");

    $title   = "registrér aktivt medlem i ny gruppe";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-usergrouprelationship&amp;page=register-usergrouprelationship";
    $fields  = Array();

    $fields[] = Array("label" => "medlem", "type" => "select",
                      "attributes" => Array("name" => "userid", "values" => $users,
                       										  "currentValue" => $currentUser));
    $fields[] = Array("label" => "gruppe", "type" => "select",
                    	"attributes" => Array("name" => "groupid", "values" => $groups));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

    $groupList   = new Groups("admin-only");
    $groups      = $groupList->getList();

    $title   = "masseregistrér medlemmer i ny gruppe";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-usergrouprelationship&amp;page=register-usergrouprelationship";
    $fields  = Array();

    $fields[] = Array("label" => "kortnummer/epost", "type" => "textarea",
                      "attributes" => Array("name" => "users", "cols" => 70, "rows" => 15,
                                            "comment" => "Tast inn kortnummer eller epost for å legge til flere brukere i samme gruppe på én gang. Én per linje."));
    $fields[] = Array("label" => "gruppe", "type" => "select",
                      "attributes" => Array("name" => "groupid", "values" => $groups));

    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

  }

  public function _displayUsers(){
    $group     = scriptParam("groupid");
    $groupList = new Groups();
    $groups    = $groupList->getList();
    $limits    = Array(Array("id" => 15, "title" => 15),
                    Array("id" => 25, "title" => 25),
                    Array("id" => 50, "title" => 50),
                    Array("id" => 100, "title" => 100),
                    Array("id" => 10000, "title" => "alle")
                    );
    $addressStatus =  Array(Array("id" => -1, "title" => "alle"),
    						Array("id" => 1, "title" => "gyldig adresse"),
    						Array("id" => 2, "title" => "ukjent adressestatus"),
    						Array("id" => 10, "title" => "gyldige og ukjente"),
    						Array("id" => 0, "title" => "ugyldig addresse"));

    $limit   = scriptParam("limit");
    if (empty($limit)){
      $limit = 15;
    }
    $current = scriptParam("groupid");
    if (empty($current)){
      $current = 2;
    }
    $addressStatusCurrent = scriptParam("validaddress");

    $title   = "vis brukere";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-users";
    $fields  = Array();

    $fields[] = Array("label" => "vis", "type" => "select",
                      "attributes" => Array("name" => "limit", "values" => $limits,
                                            "currentValue" => $limit));
    $fields[] = Array("label" => "fra", "type" => "select",
                      "attributes" => Array("name" => "groupid", "values" => $groups,
                                            "currentValue" => $current));
    $fields[] = Array("label" => "postadressestatus", "type" => "select",
                      "attributes" => Array("name" => "validaddress", "values" => $addressStatus,
                                            "currentValue" => $addressStatusCurrent));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");


    $search = scriptParam("search");
    if (empty($search)){
      $search = "";
    }

    $title   = "søk etter bruker";
    $id      = "usersearch";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-users";
    $fields  = Array();

    $fields[] = Array("label" => "søk på navn, kortnr eller e-post", "type" => "text",
                      "attributes" => Array("name" => "search", "value" => $search));
    $form = new Form($title, $enctype, $method, $action, $fields, $id);
    $form->display("horizontal");


    if (!empty($search)){
      $limit = -1;
      $users = new Users();
      $users->displayList($search, $limit, true, $addressStatusCurrent);
      print("<a href=\"#usersearch\">nytt søk</a>");
    }else if (!empty($group)){
      $users = new Users();
      $users->displayList($group, $limit, false, $addressStatusCurrent);
    }

  }

  public function _displayUserExpiries(){
    $group  = scriptParam("groupid");
    $expiry = scriptParam("expiry");
    $groupList = new Groups();
    $groups    = $groupList->getList();
    $formats = Array(Array("id" => "screen", "title" => "vis"),
                     Array("id" => "file", "title" => "last ned")
                     );
    $limits = Array(Array("id" => 15, "title" => 15),
                    Array("id" => 25, "title" => 25),
                    Array("id" => 50, "title" => 50),
                    Array("id" => 100, "title" => 100),
                    Array("id" => 10000, "title" => "alle")
                    );
    $expiries = Array(Array("id" => 'all', "title" => "uansett"),
                      Array("id" => 'lifetime', "title" => "livsvarig medlemskap"),
                      Array("id" => '0000-00-00', "title" => "ugyldig utløpsår"),
                      Array("id" => 'expired', "title" => "utløpt medlemskap"),
                      Array("id" => 'valid', "title" => "gyldig medlemskap"),
                      Array("id" => 'no-cardno', "title" => "ikke kjøpt medlemskap (etter H05)"),
                      Array("id" => 'card-not-produced', "title" => "medlemskort ikke produsert"),
                      Array("id" => 'card-not-delivered', "title" => "kort produsert, ikke levert"),
                      Array("id" => 'no-sticker', "title" => "har kort, mangler oblat"),
                      );

    $format = scriptParam("format");
    if (empty($format)){
      $format = "screen";
    }
    $limit   = scriptParam("limit");
    if (empty($limit)){
      $limit = 15;
    }
    $current = scriptParam("groupid");
    if (empty($current)){
      $current = 2;
    }
    $curExp = scriptParam("expiry");
    if (empty($curExp)){
      $curExp= NULL;
    }

    $title   = "vis brukere";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-user-expiries";
    $fields  = Array();

    $fields[] = Array("label" => "", "type" => "select",
                      "attributes" => Array("name" => "format", "values" => $formats,
                                            "currentValue" => $format));
    $fields[] = Array("label" => "", "type" => "select",
                      "attributes" => Array("name" => "limit", "values" => $limits,
                                            "currentValue" => $limit));
    $fields[] = Array("label" => "fra", "type" => "select",
                      "attributes" => Array("name" => "groupid", "values" => $groups,
                                            "currentValue" => $current));
    $fields[] = Array("label" => "med", "type" => "select",
                      "attributes" => Array("name" => "expiry", "values" => $expiries,
                                            "currentValue" => $curExp));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");


    $search = scriptParam("search");
    if (empty($search)){
      $search = "";
    }

    $title   = "søk etter bruker";
    $id      = "usersearch";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-user-expiries";
    $fields  = Array();

    $fields[] = Array("label" => "søk etter medlem", "type" => "text",
                      "attributes" => Array("name" => "search", "value" => $search));
    $form = new Form($title, $enctype, $method, $action, $fields, $id);
    $form->display("horizontal");

    if (isAdmin()){
      $code_search = scriptParam("code_search");
      if (empty($code_search)){
        $code_search = "";
      }
      $title   = "slå opp kortkode";
      $id      = "cardcode";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?page=display-user-expiries";
      $fields  = Array();

      $fields[] = Array("label" => "slå opp kode", "type" => "text",
                       "attributes" => Array("name" => "code_search", "value" => $code_search));
      $form = new Form($title, $enctype, $method, $action, $fields, $id);
      $form->display("horizontal");

      if ($code_search != ""){
        displayCode($code_search);
      }
    }

    if ($search != ""){
      $users = new Users();
      $users->displayExpiryListSearch($search);
    }else if (!empty($expiry) && $format != "file"){
      $users = new Users();
      $users->displayExpiryList($group, $limit, $expiry, $format);
    }


  }

  public function _registerUserMembershipBankpayment() {
    if (checkAuth("view-register-membership-bankpayment")) {
      if (isset($_POST["banktext"])) {
        $users = new Users();
        if($users->parseMembershipPaymentsFromBank($_POST["banktext"])) {
          return true;
        }
      }

      $title   = "Registrere betalte medlemskap fra faktura med KID";
      $id      = "membership-bankpayment";
      $enctype = NULL;
      $method = "post";
      $action  = "index.php?page=register-membership-bankpayment";
      $fields  = Array();

      $fields[] = @Array("label" => "sett inn fil mottatt fra bank",
                        "type" => "textarea",
                        "attributes" => Array("name" => "banktext",
                                              "cols" => 100,
                                              "rows" => 20,
                                              "value" => $_POST["banktext"]));
      $fields[] = @Array("label" => "",
                        "type" => "checkbox",
                        "attributes" => Array("name" => "parseonly",
                                              "label" => " analyser filen uten å oppdatere medlemskapsdatabasen",
                                              "checked" => true));
      $form = new Form($title, $enctype, $method, $action, $fields, $id);
      $form->display();

      return true;
    }

    return false;
  }

  public function _registerUserEAUpdate() {
    if (checkAuth("view-register-user-ea-update")) {
      if (isset($_POST["eatext"])) {
        $users = new Users();
        if($users->parseEAfile($_POST["eatext"])) {
          return true;
        }
      }

      $title   = "Registrere adresseoppdatering mottatt gjennom EA";
      $id      = "eaupdate";
      $enctype = NULL;
      $method = "post";
      $action  = "index.php?page=register-user-ea-update";
      $fields  = Array();

      $fields[] = @Array("label" => "sett inn CVS fil",
                        "type" => "textarea",
                        "attributes" => Array("name" => "eatext",
                                              "cols" => 100,
                                              "rows" => 20,
                                              "value" => $_POST["eatext"]));
      $form = new Form($title, $enctype, $method, $action, $fields, $id);
      $form->display();

      return true;
    }

    return false;
  }

  public function _displayUser(){
    $userId = scriptParam("userid");
    $user = new User($userId);
    $user->display();
  }

  public function _registerUser(){
    $placesOfStudy = new PlacesOfStudy();
    $pos       = $placesOfStudy->getList();
    $divisions = new Divisions();
    $divs      = $divisions->getList();
    $groups    = new Groups("admin-only");
    $groups    = $groups->getList();
    $data      = $_POST; //Error during register, old values needed to avoid retyping

		if (!isset($_SESSION['valid-user'])){?>
		<h2>Skjema for medlemsregistrering</h2>
		<p>Her kan du bli ny medlem, eller registrere ditt midlertidige medlemskap.</p>
		<p>Har du allerede fylt inn din personinfo, men ikke fullført betalingen eller lagt inn aktiveringskoden for ditt midlertidige medlemsskap? <a href="/">Logg inn her.</a></p>
		<p>Alle feltene må fylles ut.</p>

		<?php
    }

    $id      = "userForm";
    $title   = "Registrer bruker";

    $enctype = NULL;
    $method  = "post";
    if (!isset($_SESSION['valid-user'])){
	    $action  = "index.php?action=register-user&amp;page=register-membership";
    }else {
  	  $action  = "index.php?action=register-user&amp;page=display-user";
    }
    $fields  = Array();

    $fields[] = Array("label" => "Fornavn", "type" => "text",
                      "attributes" => Array("name" => "firstname" ,"size" => 50, "maxlength" => 50,
                                            "value" => (isset($data['firstname'])) ? $data['firstname'] : ""));
    $fields[] = Array("label" => "Etternavn", "type" => "text",
                      "attributes" => Array("name" => "lastname" ,"size" => 50, "maxlength" => 50,
                                            "value" => (isset($data['lastname'])) ? $data['lastname'] : ""));
    $fields[] = Array("label" => "Brukernavn", "type" => "text",
                      "attributes" => Array("name" => "username" ,"size" => 12, "maxlength" => 12,
                                            "onchange" => "checkUsername(this.value)",
																						"value" => (isset($data['username'])) ? $data['username'] : ""));
    $fields[] = Array("label" => "Passord", "type" => "password",
                      "attributes" => Array("name" => "password1"));
    $fields[] = Array("label" => "Passord (gjenta)", "type" => "password",
                      "attributes" => Array("name" => "password2"));
    $fields[] = Array("label" => "Utenlandsk adresse?", "type" => "checkbox",
                      "attributes" => Array("name" => "addresstype", "label" => " Hak av for å registrere utenlandsk adresse",
                                            "checked" => (isset($data['addresstype'])) ? "checked" : ""));
    $fields[] = Array("label" => "Gateadresse", "type" => "textarea",
                      "attributes" => Array("name" => "street", "cols" => 70, "rows" => 3,
                                            "value" => (isset($data['street'])) ? $data['street'] : "",
                                            "maxlength" => 255, "nocounter" => true));
    $fields[] = Array("label" => "Postnummer", "type" => "text",
                      "attributes" => Array("name" => "zipcode" ,"size" => 4,
                                            "maxlength" => 4,
                                            "onchange" => "checkZip(this);",
                                            "onblur" => "checkZip(this);",
                                            "value" => (isset($data['zipcode'])) ? $data['zipcode'] : ""));
    $fields[] = Array("label" => "Poststed", "type" => "text",
                      "attributes" => Array("name" => "postarea", "readonly" => "readonly",
                                            "readonly" => true));
    $fields[] = Array("label" => "By", "type" => "text",
                      "attributes" => Array("name" => "city" ,"size" => 50, "maxlength" => 50,
                                            "value" => (isset($data['city'])) ? $data['city'] : ""));
    $fields[] = Array("label" => "Stat", "type" => "text",
                      "attributes" => Array("name" => "state" ,"size" => 50, "maxlength" => 50,
                                            "value" => (isset($data['state'])) ? $data['state'] : ""));
    $fields[] = Array("label" => "Land", "type" => "text",
                      "attributes" => Array("name" => "country" ,"size" => 50, "maxlength" => 50,
                                            "id" => "country",
                                            "value" => (isset($data['country'])) ? $data['country'] : ""));
    $fields[] = Array("label" => "Telefon", "type" => "text",
                      "attributes" => Array("name" => "phonenumber" ,"size" => 16, "maxlength" => 16,
                                            "value" => (isset($data['phonenumber'])) ? $data['phonenumber'] : ""));
    $fields[] = Array("label" => "Epost", "type" => "text",
                      "attributes" => Array("name" => "email" ,"size" => 50, "maxlength" => 120,
                                            "value" => (isset($data['email'])) ? $data['email'] : ""));
    $fields[] = Array("label" => "Fødselsdato", "type" => "date",
                      "attributes" => Array("name" => "birthdate",
                                            "value" => (isset($data['birthdate'])) ? $data['birthdate'] : "yyyy-mm-dd"));
    $fields[] = Array("label" => "Studiested", "type" => "select",
                      "attributes" => Array("name" => "placeOfStudy", "values" => $pos,
                                            "currentValue" => (isset($data['placeOfStudy'])) ? $data['placeOfStudy'] : ""));
    if (!isset($_SESSION['valid-user'])) {
	    /*$fields[] = Array("label" => "aktiv i en forening?", "type" => "checkbox",
      	                "attributes" => Array("name" => "active",
      	                											"label" => " hak av om du er eller vil bli aktiv i en forening på Studentersamfundet",
          	                                  "checked" => (isset($data['active'])) ? "checked" : ""));
    	$fields[] = Array("label" => "forening", "type" => "select",
      	                "attributes" => Array("name" => "division", "values" => $divs,
        	                                    "currentValue" => (isset($data['division'])) ? $data['division'] : ""));*/

    } else {
	    $fields[] = Array("label" => "Legg til i gruppe?", "type" => "checkbox",
      	                "attributes" => Array("name" => "group",
        	                                    "label" => " Hak av for å registrere bruker i en gruppe",
          	                                  "checked" => (isset($data['group'])) ? "checked" : ""));
    	$fields[] = Array("label" => "Gruppe", "type" => "select",
      	                "attributes" => Array("name" => "group_id", "values" => $groups,
        	                                    "currentValue" => (isset($data['group_id'])) ? $data['group_id'] : ""));
    }
    $form = new Form($title, $enctype, $method, $action, $fields, $id);
    $form->display();
  }

  public function _editUser($id = NULL){
    $placesOfStudy = new PlacesOfStudy();
    $pos = $placesOfStudy->getList();
    $cardValues = Array(Array("id" => 0, "title" => "nei"),
                        Array("id" => 1, "title" => "ja"));
    $addressValues = Array(Array("id" => 0, "title" => "Ugyldig"),
						               Array("id" => 1, "title" => "Gyldig"),
						               Array("id" => 2, "title" => "Ukjent"));

    if ($id == NULL){
      $userId = scriptParam("userid");
      $tag = "";
    }else {
      $userId = $id;
      $tag = "current-";
    }
    $user   = new User($userId);

    if (checkAuth("perform-update-user") || $tag == "current-" || checkResponsible()){
      $readonly = false;
    }else {
      $readonly = true;
    }


    $title   = "$user->firstname $user->lastname - brukerinformasjon";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-".$tag."user&amp;page=display-".$tag."user";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "userid", "readonly" => true,
                                            "size" => strlen($user->id),
                                            "value" => "$userId"));
    $fields[] = Array("label" => "kortnummer", "type" => "text",
                           "attributes" => Array("name" => "cardno", "readonly" => true,
                                                 "size" => strlen($user->cardno),
                                                 "value" => $user->cardno));
    $fields[] = Array("label" => "brukernavn", "type" => "text",
                           "attributes" => Array("name" => "username", "readonly" => true,
                                                 "size" => strlen($user->username),
                                                 "value" => $user->username));
    $fields[] = Array("label" => "fornavn", "type" => "text",
                           "attributes" => Array("name" => "firstname" ,"size" => 50, "maxlength" => 50,
                                                 "readonly" => $readonly, "value" => $user->firstname));
    $fields[] = Array("label" => "etternavn", "type" => "text",
                           "attributes" => Array("name" => "lastname" ,"size" => 50, "maxlength" => 50,
                                                 "readonly" => $readonly, "value" => $user->lastname));
    $fields[] = Array("label" => "utenlandsk adresse?", "type" => "checkbox",
                      "attributes" => Array("name" => "addresstype", "label" => " Hak av for å registrere utenlandsk adresse",
                                            "readonly" => $readonly, "checked" => $user->addresstype == "int" ? "checked" : ""));
    $fields[] = Array("label" => "gateadresse", "type" => "textarea",
                           "attributes" => Array("name" => "street", "cols" => 70, "rows" => 3,
                                                 "maxlength" => 255, "nocounter" => true,
                                                 "readonly" => $readonly, "value" => $user->street));
    $fields[] = Array("label" => "postnummer", "type" => "text",
                           "attributes" => Array("name" => "zipcode", "size" => 4, "maxlength" => 4,
		                                             "onchange" => "checkZip(this);",
    		                                         "onblur" => "checkZip(this);",
                                                 "readonly" => $readonly, "value" => $user->zipcode));
    $fields[] = Array("label" => "poststed", "type" => "text",
                           "attributes" => Array("name" => "postarea", "readonly" => "readonly",
                                                 "size" => strlen($user->postarea) + 1,
                                                 "readonly" => "readonly", "value" => $user->postarea));
    $fields[] = Array("label" => "by", "type" => "text",
                           "attributes" => Array("name" => "city" ,"size" => 50, "maxlength" => 50,
                                                 "readonly" => $readonly, "value" => $user->city));
    $fields[] = Array("label" => "stat", "type" => "text",
                           "attributes" => Array("name" => "state" ,"size" => 50, "maxlength" => 50,
                                                 "readonly" => $readonly, "value" => $user->state));
    $fields[] = Array("label" => "land", "type" => "text",
                           "attributes" => Array("name" => "country" ,"size" => 50, "maxlength" => 50,
                                                 "readonly" => $readonly, "value" => $user->country));

    if (isAdmin() || checkAuth("perform-update-user-hascard")) {
      $fields[] = Array("label" => "adressestatus",
                        "type" => "select",
                        "attributes" => Array("name" => "valid_address",
                                              "disabled" => $readonly,
                                            	"values" => $addressValues,
                                            	"currentValue" => $user->validAddress));
    }

    if ($user->validAddress ==  0) {
      $validAddressMessage = "Postadressen er merket som ugyldig, vennligst undersøk om det er riktig adresse";
      $fields[] = Array("label" => "adressestatus",
                        "type" => "text",
                        "attributes" => Array("name" => "addressestatus", "readonly" => true,
                                              "size" => strlen($validAddressMessage),
                                              "value" => $validAddressMessage));
    }

    $fields[] = Array("label" => "telefon", "type" => "text",
                           "attributes" => Array("name" => "phonenumber" ,"size" => 16, "maxlength" => 16,
                                                 "readonly" => $readonly, "value" => $user->phonenumber));
    $fields[] = Array("label" => "epost", "type" => "text",
                           "attributes" => Array("name" => "email" ,"size" => 50, "maxlength" => 120,
                                                 "readonly" => $readonly, "value" => $user->email));
    $fields[] = Array("label" => "fødselsdato", "type" => "date",
                           "attributes" => Array("name" => "birthdate",
                                                 "readonly" => $readonly, "value" => $user->birthdate));
    $fields[] = Array("label" => "studiested", "type" => "select",
                           "attributes" => Array("name" => "placeOfStudy", "values" => $pos,
                                                 "disabled" => $readonly, "currentValue" => $user->placeOfStudy));
    if (isAdmin() || checkAuth("perform-update-user-hascard")) {
        $fields[] = Array("label" => "har medlemskort", "type" => "select",
                          "attributes" => Array("name" => "cardProduced", "values" => $cardValues,
                          "disabled" => $readonly, "currentValue" => $user->getCardProduced()));
    } else {
        $fields[] = Array("label" => "har medlemskort", "type" => "hidden",
                          "attributes" => Array("name" => "cardProduced",
                          "disabled" => $readonly, "value" => $user->getCardProduced()));
    }
    $fields[] = Array("label" => "har fått utlevert medlemskort", "type" => "hidden",
                      "attributes" => Array("name" => "cardDelivered",
                      "disabled" => $readonly, "value" => $user->getCardDelivered()));

    if ( isAdmin() ) {
        $migrated = is_migrated($user->id) ? '<img src="graphics/tick.png" alt="ja">' : '<img src="graphics/cross.png" alt="nei">';
        $fields[] = Array("label" => "migrert", "type" => "text",
                          "attributes" => Array("name" => "migrated", "readonly" => true,
                                                "size" => 16,
                                                "value" => $migrated));
    }

    $form = new Form($title, $enctype, $method, $action, $fields, NULL, $readonly);
    $form->display();

?>
		<span class="btn" onclick="toggleDisplay('user-groups'); toggleText(this, 'vis grupper', 'skjul grupper');">vis grupper</span>
			<div id="user-groups" style="display: none;">
<?php
    $user->displayGroups();

    if (checkAuth("view-register-usergrouprelationship")) {
    	$groupList   = new Groups("admin-only");
    	$groups      = $groupList->getList();

	    $title = NULL;
  	  $enctype = NULL;
    	$method  = "post";
	    $action  = "index.php?action=register-usergrouprelationship&amp;page=display-user";
  	  $fields  = Array();

    	$fields[] = Array("label" => "medlem", "type" => "hidden",
                      	"attributes" => Array("name" => "userid",
                       											  "value" => $user->id));
    	$fields[] = Array("label" => "registrér bruker i ny gruppe", "type" => "select",
                    		"attributes" => Array("name" => "groupid", "values" => $groups));

	    $form = new Form($title, $enctype, $method, $action, $fields);
    	$form->display("horizontal");
    }
?>
			</div>
<?php
    if(checkAuth("view-user-updates")){
?>
		<span class="btn" onclick="toggleDisplay('user-updates'); toggleText(this, 'vis endringer', 'skjul endringer');">vis endringer</span>
		<div id="user-updates" style="display: none">
<?php
      $user->displayUpdates();
      ?></div><?php
    }
    if(checkAuth("view-delete-user")){
?>
		<!--a class="btn" href="javascript: if(confirm('Slett bruker?\n\nDette kan ikke angres!')) {
		  location='index.php?action=delete-user&amp;userid=<?php print $user->id;?>'};">slett bruker</a-->
<?php
    }
  }

  public function _changeUsername(){
    $title = "Endre brukernavn";
    $user  = new User(getCurrentUser());

    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=change-username&amp;page=change-username";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "userid", "size" => 10, "maxlength" => 12,
                                            "value" => $user->id));
    $fields[] = Array("label" => "brukernavn", "type" => "text",
                      "attributes" => Array("name" => "username", "size" => 12, "maxlength" => 12,
                                            "value" => $user->username,
                                            "help" => "Om brukernavnet er i bruk på forumet kreves det at du oppgir samme passord som på forumet for å verifisere at det er ditt brukernavn."));
    $fields[] = Array("label" => "passord", "type" => "password",
                      "attributes" => Array("name" => "password"));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }


  public function _registerDocumentCategory(){
    $docCats = new DocumentCategories();
    $docCats->displayList();

    print("<div id=\"statusField\">&nbsp;</div>");

    $title   = "registrér dokumenttype";
    $enctype = NULL;
    $method  = "post";
    $action  = "javascript: addCategory('document');";
    $fields  = Array();

    $fields[] = Array("label" => "dokumenttype", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 50));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "rows" => 5, "cols" => 70));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editDocumentCategory(){
    $title   = "oppdatér dokumenttype";
    $documentcat = new DocumentCategory(scriptParam("documentcategoryid"));

    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-documentcategory&amp;page=register-documentcategory";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "documentcategoryid", "size" => 50, "maxlength" => 25,
                                            "value" => $documentcat->id));
    $fields[] = Array("label" => "stillingstype", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 25,
                                            "value" => $documentcat->title));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "rows" => 5, "cols" => 70,
                                            "value" => $documentcat->text));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _registerEventCategory(){
    $eventCats = new EventCategories();
    $eventCats->displayList();

    print("<div id=\"statusField\">&nbsp;</div>");

    $title   = "registrér aktivitetstype";
    $enctype = NULL;
    $method  = "post";
    $action  = "javascript: addCategory('event');";
    $fields  = Array();

    $fields[] = Array("label" => "aktivitetstype", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 50));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "rows" => 5, "cols" => 70));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editEventCategory(){
    $title   = "oppdatér aktivitetstype";
    $eventcat = new EventCategory(scriptParam("eventcategoryid"));

    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-eventcategory&amp;page=register-eventcategory";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "eventcategoryid", "size" => 50, "maxlength" => 25,
                                            "value" => $eventcat->id));
    $fields[] = Array("label" => "stillingstype", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 25,
                                            "value" => $eventcat->title));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "rows" => 5, "cols" => 70,
                                            "value" => $eventcat->text));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _registerJobCategory(){
    $jobCats = new JobCategories();
    $jobCats->displayList();

    print("<div id=\"statusField\">&nbsp;</div>");

    $title   = "registrér stillingstype";
    $enctype = NULL;
    $method  = "post";
    $action  = "javascript: addCategory('job');";
    $fields  = Array();

    $fields[] = Array("label" => "stillingstype", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 25));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "rows" => 5, "cols" => 70));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editJobCategory(){
    $title   = "oppdatér stillingstype";
    $jobcat = new JobCategory(scriptParam("jobcategoryid"));

    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=update-jobcategory&amp;page=register-jobcategory";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "jobcategoryid", "size" => 50, "maxlength" => 25,
                                            "value" => $jobcat->id));
    $fields[] = Array("label" => "stillingstype", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 25,
                                            "value" => $jobcat->title));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "text", "rows" => 5, "cols" => 70,
                                            "value" => $jobcat->text));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

	private function _displayProduct() {
	  ?>
		<p><a class="btn" href="index.php?page=display-webshop">tilbake til produkter</a></p>
	  <?php
	  $product = new Product(scriptParam('productid'));
	  $product->display();
	}


  public function _registerProduct(){
    $products = new Products();
    $products->displayList();

		$comment_options = array(
													array('id' => '0', 'title' => 'Ikke tillat kommentar'),
													array('id' => '1', 'title' => 'Tillat kommentar')
													);

		$display_options = array(
													array('id' => '1', 'title' => 'Vis i butikk'),
													array('id' => '0', 'title' => 'Ikke vis i butikk')
													);

    print("<div id=\"statusField\">&nbsp;</div>");

    $title   = "registrér produkt";
    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=register-product&amp;page=register-product";
    $fields  = Array();

    $fields[] = Array("label" => "produktnavn", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 255));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "description", "rows" => 5, "cols" => 70));
    $fields[] = Array("label" => "pris", "type" => "text",
                      "attributes" => Array("name" => "price", "size" => 4, "maxlength" => 4));
    $fields[] = Array("label" => "kommentarfelt", "type" => "select",
                      "attributes" => Array("name" => "allow_comment",
                                            "values" => $comment_options,
                                            "help" => "Dette feltet angir om kunder kan legge til kommentarer ved kjøp. Bruk dette dersom man skal oppgi for eksempel størrelser."));
    $fields[] = Array("label" => "vis i butikk", "type" => "select",
                      "attributes" => Array("name" => "display_in_shop",
                                            "values" => $display_options,
                                            "help" => "Dette feltet angir om produktet skal vises i nettbutikken. Dette gjelder de fleste produkter. Noen produkter (for eksempel medlemskap) skal normalt ikke vises i butikken, men kun være tilgjengelig gjennom andre sider. Dette feltet kan også brukes for å midlertidig fjerne produkter fra butikken."));
    $fields[]  =Array("label" => "bilde", "type" => "file",
                      "attributes" => Array("name" => "userfile", "size" => 55));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
  }

  public function _editProduct(){
    $title   = "oppdatér produkt";
    $product = new Product(scriptParam("productid"));
		$comment_options = array(
													array('id' => '0', 'title' => 'Ikke tillat kommentar'),
													array('id' => '1', 'title' => 'Tillat kommentar')
													);

		$display_options = array(
													array('id' => '0', 'title' => 'Ikke vis i butikk'),
													array('id' => '1', 'title' => 'Vis i butikk')
													);

    $enctype = "multipart/form-data";
    $method  = "post";
    $action  = "index.php?action=update-product&amp;page=register-product";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "productid", "size" => 50, "maxlength" => 25,
                                            "value" => $product->id));
    $fields[] = Array("label" => "produktnavn", "type" => "text",
                      "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 255,
                                            "value" => $product->title));
    $fields[] = Array("label" => "beskrivelse", "type" => "textarea",
                      "attributes" => Array("name" => "description", "rows" => 5, "cols" => 70,
                                            "value" => $product->description));
    $fields[] = Array("label" => "pris", "type" => "text",
                      "attributes" => Array("name" => "price", "size" => 4, "maxlength" => 4,
                                            "value" => $product->price));
    $fields[] = Array("label" => "kommentarfelt", "type" => "select",
                      "attributes" => Array("name" => "allow_comment",
                                            "currentValue" => $product->allow_comment, "values" => $comment_options,
                                            "help" => "Dette feltet angir om kunder kan legge til kommentarer ved kjøp. Bruk dette dersom man skal oppgi for eksempel størrelser."));
    $fields[] = Array("label" => "vis i butikk", "type" => "select",
                      "attributes" => Array("name" => "display_in_shop",
                                            "currentValue" => $product->display_in_shop, "values" => $display_options,
                                            "help" => "Dette feltet angir om produktet skal vises i nettbutikken. Dette gjelder de fleste produkter. Noen produkter (for eksempel medlemskap) skal normalt ikke vises i butikken, men kun være tilgjengelig gjennom andre sider. Dette feltet kan også brukes for å midlertidig fjerne produkter fra butikken."));
    $fields[]  =Array("label" => "bilde", "type" => "file",
                      "attributes" => Array("name" => "userfile", "size" => 55));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();

    $product->displayBuyers();
  }

  public
  function _registerMembership(){
    ?>
    <h3>Har du kjøpt medlemskap i en av barene på Studentersamfundet?</h3>
		<p>Du skal ha fått med et eget kort med aktiveringsnummer og aktiveringspassord.</p>

    <?php
    $title   = "registrér medlemskap";

    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-membership";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "userid", "size" => 12, "maxlength" => 10,
                                            "value" => getCurrentUser()));
    $fields[] = Array("label" => "aktiveringsnummer", "type" => "text",
                      "attributes" => Array("name" => "cardno", "size" => 12, "maxlength" => 10));
    $fields[] = Array("label" => "aktiveringspassord", "type" => "text",
                      "attributes" => Array("name" => "verificationCode", "size" => 12, "maxlength" => 20));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
    
    
    if(time() < 1315612799):
    ?>
    <style type="text/css">
    #lol
    {
    	margin:10px 0px;
    }
    
    	#lol:after
    	{
    		content:".";
    		clear:both;
    		height:0px;
    		display:block;
    		visibility:hidden;
    	}
    	
    	#lol .half
    	{
    		width:45%;
    		float:left;
    		
    	}
    </style>
   <div id="lol">
   <div class="half">
    <h3>Ny i DNS?</h3>
   
    <p>Har du lyst til å være med å vår egen lille fadderhelg 9.-11. september? Bli kjent med Det Norske Studentersamfund og vi som holder til der! Se <a href="http://studentersamfundet.no/nyidns.php">ny i dns!</a></p>
 </div>
 <div class="half">
 	<h3>Bli aktiv!</h3>
 	<p>Syns du noe av det vi holder på med er spennende? Lyst til å booke band eller lære å mikse drinker? Bli med oss som aktiv i en av de mange foreningene på huset, du får garantert minner for livet og nye venner! Se <a href="http://studentersamfundet.no/bliaktiv.php">bli aktiv!</a></p>
 
 </div>
 </div>
 
 <?php endif;
    if (checkAuth("view-register-membership-payex")) {
    	?>
    	<br />
    	<h3>Vil du betale med VISA-kort?</h3>
			<?php
			$product = new Product(1);
			$product->display();
    }
  }

  public
  function _renewMembership(){
    $title   = "aktiver medlemskap";

    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=renew-membership";
    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "userid", "size" => 12, "maxlength" => 10,
                                            "value" => getCurrentUser()));
    $fields[] = Array("label" => "aktiveringsnummer", "type" => "text",
                      "attributes" => Array("name" => "cardno", "size" => 12, "maxlength" => 10,
                                            "comment" => "Bruk aktiveringsnummeret på arket du fikk da du kjøpte medlemskapet ditt."));
    $fields[] = Array("label" => "aktiveringskode", "type" => "text",
                      "attributes" => Array("name" => "verificationCode", "size" => 12, "maxlength" => 20));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
    if (checkAuth("view-register-membership-payex")) {
    	?>
    	<br />
    	<h3>Vil du betale med VISA-kort?</h3>
			<?php
			$product = new Product(1);
			$product->display();
    }

  }

  public
  function _payexForm($title = "test payex", $type = NULL, $value = NULL, $product_id = NULL){

    $enctype = NULL;
    $method  = "post";
    if ($type == "membership") {
    	$action  = "index.php?action=register-membership-payex";
    	$product_id = 1;
    }else if ($type ="galla"){
    	$action  = "index.php?action=payex-transaction";

    }

    if ($product_id != NULL){
    	$product = new Product($product_id);
    }

    $trans_id = createTransactionId();

    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "userid", "size" => 12, "maxlength" => 10,
                                            "value" => getCurrentUser()));
    $fields[] = Array("label" => "product id", "type" => "hidden",
                      "attributes" => Array("name" => "productid", "size" => 12, "maxlength" => 10,
                                            "value" => $product_id));
    $fields[] = Array("label" => "trans id", "type" => "hidden",
                      "attributes" => Array("name" => "transactionid", "size" => 12, "maxlength" => 20,
                                            "value" => $trans_id));
    if ($value == NULL) {
     	if (@$product != NULL){
    		$fields[] = Array("label" => "beløp", "type" => "text",
      		                "attributes" => Array("name" => "amount", "size" => 5, "maxlength" => 5,
																							"value" => $product->price, "readonly" => "readonly"));
     	}else {
	    	$fields[] = Array("label" => "beløp", "type" => "text",
      	                "attributes" => Array("name" => "amount", "size" => 5, "maxlength" => 5));
     	}
 	  }else {
    	$fields[] = Array("label" => "beløp", "type" => "text",
      	                "attributes" => Array("name" => "amount", "size" => 5, "maxlength" => 5,
																							"value" => $value, "readonly" => "readonly"));
    }

    $fields[] = Array("label" => "VISA-kortnummer", "type" => "text",
                      "attributes" => Array("name" => "cardNumber", "size" => 16, "maxlength" => 16));
    $fields[] = Array("label" => "utløpsmåned", "type" => "text",
                      "attributes" => Array("name" => "cardNumberExpireMonth", "size" => 2, "maxlength" => 2));
    $fields[] = Array("label" => "utløpsår", "type" => "text",
                      "attributes" => Array("name" => "cardNumberExpireYear", "size" => 2, "maxlength" => 2));
    $fields[] = Array("label" => "cvc", "type" => "text",
                      "attributes" => Array("name" => "cardNumberCVC", "size" => 3, "maxlength" => 3));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
    ?>
    <p><img src="graphics/payex.png" alt="" style="display: inline;"/> Betalingen håndteres av <a href="http://www.payex.no/">Payex</a>.</p>
    <?php
  }

	public
	function _registerPayexTransactionConfirm() {
		$trans = new Transaction(scriptParam("transactionid"));
		$trans->display();
	}

	public
	function _transactionConfirmation() {
		$trans = new Transaction(scriptParam("transactionid"));
		$trans->displayConfirmation();
	}

  public
  function _displayBugReports(){
    ?>
    <p>
    	<a class="btn" href="index.php?page=display-bugreports&amp;selection=all">vis alle</a>
    	<a class="btn" href="index.php?page=display-bugreports&amp;selection=old">vis behandlede</a>
    	<a class="btn" href="index.php?page=display-bugreports&amp;selection=new">vis ikke behandlede</a>
		</p>
    <?php
    $selection = scriptParam("selection");
    $bugs = new BugReports($selection);
    $bugs->displayList();
  }

  public
  function _displayBugReport(){
    $bugId = scriptParam("bugreportid");
    $bug = new BugReport($bugId);
    $bug->display();
  }

  public function _displayWebshop(){
		$products = new Products();
		$products->displayShopProducts();
  }

  public function _displaySales(){
		$products = new Products();
		$products->displayList();
  }

  public function _displaySalesItem(){
		$product = new Product(scriptParam('productid'));
    $product->displayBuyers();
  }

  public
  function _displayCart(){
    $order_id = scriptParam("order_id");
    if (!empty($order_id)){
	    $order = new Order($order_id);
			if ($order->user_id != getCurrentUser()) {
				notify("Ugyldig ordrenummer. Gå tilbake og prøv igjen.");
				return;
			}
    	$order->display();
    }else {
    	print("<p>Ingen aktive handlekurverer registert.</p>");
    	$user = new User(getCurrentUser());
    	$user->displayOrders();
    }
  }

  public
  function _displayCarts(){
    $order_status = scriptParam("orderstatus");
    $order_status_list = new OrderStatuses();
    $list    = $order_status_list->getList();

    $status= scriptParam("orderstatus");
    if (empty($status)){
      $status = 1;
    }

    $title   = "vis handlekurver med status";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?page=display-carts";
    $fields  = Array();

    $fields[] = Array("label" => "vis handlekurver med status", "type" => "select",
                      "attributes" => Array("name" => "orderstatus", "values" => $list,
                                            "currentValue" => $status));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display("horizontal");

  	$user = new User(getCurrentUser());
  	$user->displayOrders($status);
  }

  public
  function _cartCheckout(){
    $order_id = scriptParam("order_id");
		$order = new Order($order_id);
		if ($order->user_id != getCurrentUser()) {
			notify("Ugyldig ordrenummer. Gå tilbake og prøv igjen.");
			return;
		}

 		$order->displayShortList();

 		$title   = "Utfør betaling";
    $enctype = NULL;
    $method  = "post";
   	$action  = "index.php?action=cart-checkout";


    $trans_id = createTransactionId();
		$value = $order->calculateTotalAmount();

    $fields  = Array();

    $fields[] = Array("label" => "id", "type" => "hidden",
                      "attributes" => Array("name" => "userid", "size" => 12, "maxlength" => 10,
                                            "value" => getCurrentUser()));
    $fields[] = Array("label" => "order id", "type" => "hidden",
                      "attributes" => Array("name" => "order_id", "size" => 12, "maxlength" => 10,
                                            "value" => $order_id));
    $fields[] = Array("label" => "trans id", "type" => "hidden",
                      "attributes" => Array("name" => "transactionid", "size" => 12, "maxlength" => 20,
                                            "value" => $trans_id));
   	$fields[] = Array("label" => "beløp", "type" => "text",
      	                "attributes" => Array("name" => "amount", "size" => 5, "maxlength" => 5,
																							"value" => $value, "readonly" => "readonly"));
    $fields[] = Array("label" => "VISA-kortnummer", "type" => "text",
                      "attributes" => Array("name" => "cardNumber", "size" => 16, "maxlength" => 16));
    $fields[] = Array("label" => "utløpsmåned", "type" => "text",
                      "attributes" => Array("name" => "cardNumberExpireMonth", "size" => 2, "maxlength" => 2));
    $fields[] = Array("label" => "utløpsår", "type" => "text",
                      "attributes" => Array("name" => "cardNumberExpireYear", "size" => 2, "maxlength" => 2));
    $fields[] = Array("label" => "cvc", "type" => "text",
                      "attributes" => Array("name" => "cardNumberCVC", "size" => 3, "maxlength" => 3,
                      											"help" => "CVC-koden finner du på baksiden av VISA-kortet ditt, som oftest i signaturfeltet. Det er de tre siste sifrene som skal brukes."));
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();
    ?>
    <p><img src="graphics/payex.png" alt="" style="display: inline;"/> Betalingen håndteres av <a href="http://www.payex.no/">Payex</a>.</p>
    <?php

  }

  public
  function _displaySmsLog() {
	  if (!isset($conn)) {
      $conn = & DB :: connect(getDSN());
	  }

    if (DB :: isError($conn)) {
        error("error: " . $conn->toString());
    }

    $since = scriptParam("since") ? scriptParam("since") : date('Y-m-d', strtotime( getNextMembershipExpiryDate() . " -1 year" ) );

    $sql = "SELECT * FROM din_sms_received WHERE `date` > '$since' ORDER BY date";
    $result = $conn->query($sql);
    if (DB::isError($result) == true){
      error($result->toString());
      return false;
    }
    if ($result->numRows() > 0) {
?>
   <h3>Mottatte SMS</h3>
    <p>Antall treff: <?php print($result->numRows()); ?></p>
    <p>Viser bare SMS-er fra etter <?php echo $since; ?>. <a href="https://inside.studentersamfundet.no/index.php?page=display-sms-log&since=2003">Vis alle</a>.
<table class="sortable" id="userlist">
  <tr>
    <th>smsid</th>
    <th>userid</th>
    <th>gsm</th>
    <th>shortno</th>
    <th>kodeord</th>
    <th>message</th>
    <th>action performed</th>
    <th>date</th>
    <th>simulation</th>
  </tr>
<?php
      while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        print "<tr>";
        print "<td class=\"number\">" . $row->smsid . "</td>";
        print "<td class=\"number\">" . $row->userid . "</td>";
        print "<td>" . $row->gsm . "</td>";
        print "<td>" . $row->shortno . "</td>";
        print "<td>" . $row->codeword . "</td>";
        print "<td>" . $row->message . "</td>";
        print "<td>" . $row->action . "</td>";
        print "<td>" . $row->date . "</td>";
        print "<td>" . $row->simulation . "</td>";
        print "</tr>\n";
      }
      print "</table>";
    } else {
      print "<p>Ingen SMS registrert.</p>";
    }


    $sql = "SELECT * FROM din_sms_sent ORDER BY date";
    $result = $conn->query($sql);
    if (DB::isError($result) == true){
      error($result->toString());
      return false;
    }
    if ($result->numRows() > 0) {
?>
   <h3>Sendte SMS</h3>
    <p>Antall treff: <?php print($result->numRows()); ?></p>
<table class="sortable" id="userlist">
  <tr>
    <th>smsid</th>
    <th>response to</th>
    <th>eurobate msgid</th>
    <th>sender</th>
    <th>receiver</th>
    <th>message</th>
    <th>codeword</th>
    <th>billing price</th>
    <th>use dlr</th>
    <th>date</th>
    <th>simulation</th>
  </tr>
<?php
      while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        print "<tr>";
        print "<td class=\"number\">" . $row->smsid . "</td>";
        print "<td class=\"number\">" . $row->response_to . "</td>";
        print "<td class=\"number\">" . $row->msgid . "</td>";
        print "<td>" . $row->sender . "</td>";
        print "<td>" . $row->receiver . "</td>";
        print "<td>" . $row->message . "</td>";
        print "<td>" . $row->codeword . "</td>";
        print "<td>" . ($row->billing_price)/100 . ",-</td>";
        print "<td>" . $row->use_dlr . "</td>";
        print "<td>" . $row->date . "</td>";
        print "<td>" . $row->simulation . "</td>";
        print "</tr>\n";
      }
      print "</table>";
    } else {
      print "<p>Ingen SMS registrert.</p>";
    }
  }

  public function _membershipSale() {
    $cardno = scriptParam("cardno");
    $fornavn = ucfirst(scriptParam("fornavn"));
    $etternavn = ucfirst(scriptParam("etternavn"));
    $epost = scriptParam("epost");

    if (!empty($cardno)) {
      $search = $cardno;
    } elseif (empty($fornavn) && empty($etternavn)) {
      $search = "";
    } else {
      $search = trim($fornavn . " " . $etternavn);
    }

      $title   = "Søk på kortnummer, fornavn og/eller etternavn";
      $id      = "usersearch";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?page=".$this->page;
      $fields  = Array();

    $fields[] = Array("label" => "Kortnummer", "type" => "text",
                       "attributes" => Array("name" => "cardno", "value" => $cardno));
    $fields[] = Array("label" => "Fornavn", "type" => "text",
                        "attributes" => Array("name" => "fornavn", "value" => $fornavn));
    $fields[] = Array("label" => "Etternavn", "type" => "text",
                        "attributes" => Array("name" => "etternavn", "value" => $etternavn));
      $form = new Form($title, $enctype, $method, $action, $fields, $id);
      $form->display("table");

    if (!empty($search)) {
      $limit = -1;
      $users = new Users();
      $users->getList($search, $limit, true);

      if ($users->users->numRows() > 0){
        print "<p>Fant " . $users->users->numRows() . " forslag til medlemmer i medlemsdatabasen.</p>\n";

        print "<table class=\"sortable\" id=\"userlist\">\n";
        print "<tr>";
        print "<th>kortnr</th>";
        print "<th>navn</th>";
        print "<th>medlem</th>";
        print "<th>kort</th>";
        print "<th>oblat</th>";
        print "<th>oppdater til</th>";
        print "</tr>";
        while ($row =& $users->users->fetchRow(DB_FETCHMODE_OBJECT)){
          $user = new User($row->id);
          print "<tr>";
          print "<td class=\"number\" id=\"user_" . $user->getId() . "_cardno\">". $user->cardno . "</td>\n";
          print "<td><a href=\"index.php?page=display-user&amp;userid=" . $user->id . "\">" . $user->getName() . "</a></td>";
          print "<td class=\"is_member\">";
          if ($user->hasExpired()) print '<img src="graphics/cross.png" alt="nei" />';
          else print '<img src="graphics/tick.png" alt="ja" />';
          print "</td>";

          print "<td class=\"is_member\">";
          if ($user->getCardDelivered()) {
            print '<img src="graphics/tick.png" alt="ja" />';
          } elseif ($user->getCardProduced()) { 
            print '<img src="graphics/user_go.png" alt="ja" />';
          } else { 
            print '<img src="graphics/cross.png" alt="nei" />';
          }
          print "</td>";

          print "<td class=\"is_member\">" . $user->getLastSticker() . "</td>";

          print "<td>";
          print "<form action=\"" . $action . "\" method=\"post\">";
          print "<input type=\"hidden\" name=\"action\" value=\"membership-sale\" />";
          print "<input type=\"hidden\" name=\"userid\" value=\"" . $user->getId() . "\" />";
          if ($user->hasExpired()) {
            if (!$user->getCardProduced()) {
              print "forny (produser kort)";
            } elseif (!$user->getCardDelivered()) {
              print "lever ut kort";
            } else {
              print "<input type=\"hidden\" name=\"subaction\" value=\"sticker-sale\" />";
              print "<select name=\"new-sticker-date\">\n";
              print "<option value=\"" . getExpiryDate("now") . "\">" . "i år (" . getStickerPeriod(getExpiryDate('now')) . ")" . "</option>\n";
              print "<option value=\"" . getExpiryDate("+1 year") . "\">" . "neste år (" . getStickerPeriod(getExpiryDate("+1 year")) . ")" . "</option>\n";
              print "<option value=\"" . getExpiryDate("+3 year") . "\">" . "tre år (" . getStickerPeriod(getExpiryDate("+3 year")) . ")" . "</option>\n";
              print "<option value=\"" . getExpiryDate("+5 year") . "\">" . "fem år (" . getStickerPeriod(getExpiryDate("+5 year")) . ")" . "</option>\n";
              print "</select>\n";
              print "<input type=\"submit\" value=\"Selg oblat\" />";
            }
          } else {
          
            if (!$user->getCardProduced()) {
              print "produser kort";
            } elseif (!$user->getCardDelivered()) {
              print "<input type=\"hidden\" name=\"subaction\" value=\"give-card\" />";
              print "<input type=\"hidden\" name=\"new-sticker-date\" value=\"" . getStickerPeriod($user->expires) . "\" />";
              print "<input type=\"submit\" name=\"give-card\" value=\"Lever ut kort (" . getStickerPeriod($user->expires) . ")\" />";
            } elseif (!$user->hasCardSticker()) {
              print "<input type=\"hidden\" name=\"subaction\" value=\"give-sticker\" />";
              print "<input type=\"hidden\" name=\"new-sticker-date\" value=\"" . getStickerPeriod($user->expires) . "\" />";
              print "<input type=\"submit\" name=\"give-sticker\" value=\"Gi oblat (" . getStickerPeriod($user->expires) . ")\" />";
            } else {
              print "-";
            }
          }
          print "</form>";
          print "</td>";

          print "<td>";
          print "<form action=\"" . $action . "\" method=\"post\">";
          print "<input type=\"hidden\" name=\"action\" value=\"membership-sale\" />";
          print "<input type=\"hidden\" name=\"userid\" value=\"" . $user->getId() . "\" />";
          if ($user->getCardDelivered()) {
            print "<input type=\"hidden\" name=\"subaction\" value=\"order-new-card\" />";
            print "<input type=\"submit\" name=\"order-new-card\" value=\"Bestill nytt kort\" />";
          }
          print "</form>";
          print  "</td>";

          print "</tr>";
        }
        print "</table>";
      } else {
        print "<p>Ingen tidligere medlemmer funnet innenfor valgte søk.</p>";
      }

      /*
         $title   = "Selg medlemskap til ny bruker";
         $id      = "usersearch";
         $enctype = NULL;
         $method  = "post";
         $action  = "index.php?page=".$this->page;
         $fields   = Array();
         $fields[] = Array("label" => "Fornavn", "type" => "text",
         "attributes" => Array("name" => "", "value" => $fornavn));
         $fields[] = Array("label" => "Etternavn", "type" => "text",
         "attributes" => Array("name" => "", "value" => $etternavn));
         $fields[] = Array("label" => "E-post", "type" => "text",
         "attributes" => Array("name" => "epost", "value" => $epost));
         $form = new Form($title, $enctype, $method, $action, $fields, $id);
         $form->display("table");
      */
    }
  }

  public function _membercardProduction() {

  }

}

?>
