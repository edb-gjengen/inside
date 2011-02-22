<?php
class ActionParser {
  var $action;

  function ActionParser() {
    $this->__construct();
  }

  function __construct() {
    $this->action = scriptParam('action');
  }

  public function performAction() {
    //Actions that don't require login
    switch ($this->action) {
      case 'order-password' :
        $this->_orderPassword();
        break;
      case 'email-lookup' :
        $this->_emailLookup();
        break;
      default :
        break;
    }

    if (isAdmin()) {
      //On-the-fly generation of files
      if (scriptParam('page') == 'display-user-expiries' && isset ($_POST['format']) && $_POST['format'] == 'file') {
        $users = new Users();
        $users->displayExpiryList(scriptParam('groupid'), scriptParam('limit'), scriptParam('expiry'), scriptParam('format'));
      }
    }

    //Actions that require that the user is logged in and has the right privileges
    if (checkAuth('perform-' . $this->action) || checkResponsible()) {
      switch ($this->action) {
        case 'log-in' :
          $this->_logIn();
          break;

        case 'log-out' :
          $this->_logOut();
          break;

        case 'update-password' :
          $this->_updatePassword();
          break;

        case 'payex-transaction' :
          $this->_payexTransaction();
          break;

        case 'register-job' :
          $this->_registerJob();
          break;

        case 'update-job' :
          $this->_updateJob();
          break;

        case 'delete-job' :
          $this->_deleteJob();
          break;

        case 'register-barshift' :
          $this->_registerBarShift();
          break;

        case 'update-barshift' :
          $this->_updateBarShift();
          break;

        case 'repeat-barshift' :
          $this->_repeatBarShift();
          break;

        case 'copy-barshift' :
          $this->_copyBarShift();
          break;

        case 'delete-barshift' :
          $this->_deleteBarShift();
          break;

        case 'register-barshiftworker' :
          $this->_registerBarshiftWorker();
          break;

        case 'register-barshiftworker-self' :
          $this->_registerBarshiftWorkerSelf();
          break;

        case 'delete-barshiftworker' :
          $this->_deleteBarShiftWorker();
          break;

        case 'register-division' :
          $this->_registerDivision();
          break;

        case 'update-division' :
          $this->_updateDivision();
          break;

        case 'delete-division' :
          $this->_deleteDivision();
          break;

        case 'register-position' :
          $this->_registerPosition();
          break;

        case 'update-position' :
          $this->_updatePosition();
          break;

        case 'delete-position' :
          $this->_deletePosition();
          break;

        case 'register-article' :
          $this->_registerArticle();
          break;

        case 'update-article' :
          $this->_updateArticle();
          break;

        case 'delete-article' :
          $this->_deleteArticle();
          break;

        case 'register-event' :
          $this->_registerEvent();
          break;

        case 'update-event' :
          $this->_updateEvent();
          break;

        case 'repeat-event' :
          $this->_repeatEvent();
          break;

        case 'copy-event' :
          $this->_copyEvent();
          break;

        case 'delete-event' :
          $this->_deleteEvent();
          break;

        case 'register-eventcomment' :
          $this->_registerEventComment();
          break;

        case 'delete-eventcomment' :
          $this->_deleteEventComment();
          break;

        case 'register-concert' :
          $this->_registerConcert();
          break;

        case 'update-concert' :
          $this->_updateConcert();
          break;

        case 'register-concertreport' :
          $this->_registerConcertReport();
          break;

        case 'update-concertreport' :
          $this->_updateConcertReport();
          break;

        case 'repeat-concert' :
          $this->_repeatConcert();
          break;

        case 'copy-concert' :
          $this->_copyConcert();
          break;

        case 'delete-concert' :
          $this->_deleteConcert();
          break;

        case 'register-weekprogram' :
          $this->_registerWeekProgram();
          break;

        case 'register-programselection' :
          $this->_registerProgramSelection();
          break;

        case 'update-programselection' :
          $this->_updateProgramSelection();
          break;

        case 'upload-document' :
          $this->_uploadDocument();
          break;

        case 'update-document' :
          $this->_updateDocument();
          break;

        case 'delete-document' :
          $this->_deleteDocument();
          break;

        case 'register-user' :
          $this->_registerUser();
          break;

        case 'update-user' :
          $this->_updateUser();
          break;

        case 'delete-user' :
          $this->_deleteUser();
          break;

        case 'change-username' :
          $this->_changeUsername();
          break;

        case 'update-user-expiry' :
          $this->_updateUserExpiry();
          break;

        case 'update-user-last-sticker' :
          $this->_updateUserLastSticker();
          break;

        case 'update-current-user' :
          $this->_updateUser(true);
          break;

        case 'update-user-division-request' :
          $this->_updateUserDivisionRequest();
          break;

        case 'register-membership' :
          $this->_registerMembership();
          break;

        case 'register-membership-payex' :
          $this->_registerMembershipPayex();
          break;

        case 'register-membership-bankpayment' :
          $this->_registerUserMembershipBankpayment();
          break;

        case 'renew-membership' :
          $this->_renewMembership();
          break;

        case 'payex-test-transaction' :
          $this->_payexTestTransaction();
          break;

        case 'cart-add-product' :
          $this->_cartAddProduct();
          break;

        case 'update-cart' :
          $this->_updateCart();
          break;

        case 'cart-checkout' :
          $this->_cartCheckout();
          break;

        case 'transaction-return' :
          $this->_transactionReturn();
          break;

        case 'delete-order' :
          $this->_deleteOrder();
          break;

        case 'grant-cardno' :
          $this->_grantCardno();
          break;

        case 'update-user-hascard' :
          $this->_updateUserHasCard();
          break;

        case 'register-group' :
          $this->_registerGroup();
          break;

        case 'update-group' :
          $this->_updateGroup();
          break;

        case 'delete-group' :
          $this->_deleteGroup();
          break;

        case 'register-action' :
          $this->_registerAction();
          break;

        case 'update-action' :
          $this->_updateAction();
          break;

        case 'delete-action' :
          $this->_deleteAction();
          break;

        case 'register-actiongrouprelationship' :
          $this->_registerActionGroupRelationship();
          break;

        case 'register-usergrouprelationship' :
          $this->_registerUserGroupRelationship();
          break;

        case 'delete-usergrouprelationship' :
          $this->_deleteUserGroupRelationship();
          break;

        case 'switch-formtype' :
          $this->_switchFormtype();
          break;

        case 'switch-tinymce-theme' :
          $this->_switchTinyMCETheme();
          break;

        case 'register-documentcategory' :
          $this->_registerDocumentCategory();
          break;

        case 'update-documentcategory' :
          $this->_updateDocumentCategory();
          break;

        case 'delete-documentcategory' :
          $this->_deleteDocumentCategory();
          break;

        case 'register-eventcategory' :
          $this->_registerEventCategory();
          break;

        case 'update-eventcategory' :
          $this->_updateEventCategory();
          break;

        case 'delete-eventcategory' :
          $this->_deleteEventCategory();
          break;

        case 'register-jobcategory' :
          $this->_registerJobCategory();
          break;

        case 'update-jobcategory' :
          $this->_updateJobCategory();
          break;

        case 'delete-jobcategory' :
          $this->_deleteJobCategory();
          break;

        case 'register-product' :
          $this->_registerProduct();
          break;

        case 'update-product' :
          $this->_updateProduct();
          break;

        case 'delete-product' :
          $this->_deleteProduct();
          break;

        case 'register-bugreport' :
          $this->_registerBugReport();
          break;

        case 'update-bugreport-status' :
          $this->_updateBugReportStatus();
          break;

        case 'delete-bugreport' :
          $this->_deleteBugReport();
          break;

        case 'membership-sale':
          $this->_membershipSale();
          break;

        case 'membercard-production':
          $this->_membercardProduction();
          break;

      }

    } else
      if ($this->action == 'register-user') {
        if ($this->_registerUser()) {
          $GLOBALS['extraScriptParams']['password'] = scriptParam('password1');
          $this->_logIn();
        }
      }
  }

  public function _orderPassword() {
    $conn = db_connect();
    $userid = trim(scriptParam('userid'));
    if (is_numeric($userid)) {
      $sql = "SELECT firstname, lastname, username, email
      			                          	          FROM din_user
      			                            	        WHERE id = $userid";
    } else {
      $sql = "SELECT firstname, lastname, username, email
      			                          	          FROM din_user
      			                            	        WHERE email = '$userid'";
    }
    $result = $conn->query($sql);
    if (DB :: isError($result) == true) {
      notify('Tjenesten er midlertidig utilgjengelig, vennligst fors&#248;k igjen senere.');
      return false;
    }
    if ($result->numRows() > 0) {
      $row = $result->fetchRow(DB_FETCHMODE_OBJECT);
      if ($row->email == '') {
        notify('Ingen epostadresse er registrert p&#229; din bruker.');
        return false;
      }
      $newPassword = generatePassword();
      if (is_numeric($userid)) {
        $sql = 'UPDATE din_user SET ' .
        "  password = PASSWORD('$newPassword'), " .
        '  passwordReset = 1 ' .
        'WHERE' .
        "  id = $userid";
      } else {
        $sql = 'UPDATE din_user SET ' .
        "  password = PASSWORD('$newPassword'), " .
        '  passwordReset = 1 ' .
        'WHERE' .
        "  email = '$userid'";
      }
      $result = $conn->query($sql);
      if (DB :: isError($result) == true) {
        notify('Tjenesten er midlertidig utilgjengelig, vennligst fors&#248;k igjen senere.');
        return false;
      }
      $sendto = $row->email;
      $subject = 'Brukernavn og passord for Studentersamfundet Inside';
      $message = "Hei, $row->firstname $row->lastname!\n " .
      "\n" .
      "\nDu har bestilt nytt brukernavn og passord til Studentersamfundet Inside. " .
      "\n" .
      "\nDitt brukernavn er: $row->username" .
      "\nDitt passord er: $newPassword" .
      "\n" .
      "\nN&#229;r du logger p&#229; vil du bli bedt om &#229; endre passord til noe som er lettere &#229; huske. Du kan ogs&#229; endre brukernavnet ditt." .
      "\n" .
      "\nhttp://www.studentersamfundet.no/inside/index.php#login" .
      "\n" .
      "\nmvh" .
      "\nStudentersamfundet Inside";
      $headers = 'From: dns.inside@studentersamfundet.no' . "\r\n";
      if (mail($sendto, $subject, $message, $headers)) {
        notify('Nytt brukernavn og passord er sendt til din registrerte epostadresse. Bruk skjemaet under for &#229; logge deg inn.');
      } else {
        notify('Det oppstod en feil under sending av epost. Vennligst kontakt' . '<a href="mailto:support@studentersamfundet.no">webansvarlig</a>.');
      }
    } else {
      if (is_numeric($userid)) {
        notify('Kortnummeret er er ikke registrert i databasen. Vennligst registr&#233;r deg f&#248;rst.');
      } else {
        notify('Ingen bruker er registrert p&#229; epostadressen du oppgav.');
      }
    }
  }

  public function _updatePassword() {
    $password1 = scriptParam('password1');
    $password2 = scriptParam('password2');
    if (strlen($password1) < 4) {
      notify('Passordet ditt m&#229; v&#230;re minst fire tegn.');
      $GLOBALS['extraScriptParams']['page'] = 'reset-password';
      return;
    } else {
      if ($password1 != $password2) {
        notify('Passordene du tastet inn var forskjellige.');
        $GLOBALS['extraScriptParams']['page'] = 'reset-password';
        return;
      } else {
        $conn = db_connect();
        $sql = sprintf("UPDATE din_user SET
        				  password      = PASSWORD(%s),
        				  passwordReset = %s
        				WHERE
        				  id = %s", $conn->quoteSmart($password1), $conn->quoteSmart(0), $conn->quoteSmart(getCurrentUser()));
        $result = $conn->query($sql);
        if (DB :: isError($result) != true) {
          notify('Nytt passord er lagret.');
        } else {
          $GLOBALS['extraScriptParams']['page'] = 'reset-password';
          notify('Problemer med lagring passord, vennligst pr&#248;v igjen senere. Inntil videre gjelder ditt gamle passord.');
        }
      }
    }
  }

  function _emailLookup() {
    $conn = db_connect();
    $email = trim(scriptParam('email'));
    if ($email == '') {
      notify('Du oppga ingen epostadresse. Bruk skjemaet under for &#229; registrere deg eller g&#229; <a href="http://www.studentersamfundet.no/medlem/">tilbake</a> og pr&#248;v igjen.');
      $GLOBALS['extraScriptParams']['page'] = 'register-user';
      return false;
    }
    $sql = "SELECT firstname, lastname, username, email
    		FROM din_user
    		WHERE email = '$email'";
    $result = $conn->query($sql);
    if (DB :: isError($result) == true) {
      notify("Tjenesten er midlertidig utilgjengelig, vennligst fors&#248;k igjen senere.");
      return false;
    }
    if ($result->numRows() > 0) {
      $row = $result->fetchRow(DB_FETCHMODE_OBJECT);
      $newPassword = generatePassword();
      $sql = "UPDATE din_user SET " .
      "  password = PASSWORD('$newPassword'), " .
      "  passwordReset = 1 " .
      "WHERE" .
      "  email = '$email'";
      $result = $conn->query($sql);
      if (DB :: isError($result) == true) {
        notify("Tjenesten er midlertidig utilgjengelig, vennligst fors&#248;k igjen senere.");
        return false;
      }
      $sendto = $row->email;
      $subject = "Brukernavn og passord for Studentersamfundets medlemsider";
      $message = "Hei, $row->firstname $row->lastname!\n " .
      "\n" .
      "\nDu er registrert i Studentersamfundets medlemsdatabase. F&#248;lg linken under og logg p&#229; med f&#248;lgende informasjon: " .
      "\n" .
      "\nDitt brukernavn er: $row->username" .
      "\nDitt passord er: $newPassword" .
      "\n" .
      "\nN&#229;r du logger p&#229; vil du bli bedt om &#229; endre passord til noe som er lettere &#229; huske. Du kan ogs&#229; endre brukernavnet ditt. " .
      "\n" .
      "\nEtter at dette er gjort vil du finne en link for registrering av fornyet medlemskap." .
      "\n" .
      "\nhttp://www.studentersamfundet.no/inside#login" .
      "\n" .
      "\nmvh" .
      "\nStudentersamfundet";
      $headers = 'From: medlemskort@studentersamfundet.no' . "\r\n";
      if (mail($sendto, $subject, $message, $headers)) {
        notify("Din epostadresse er registrert i systemet v&#229;rt.");
        notify("En epost er sendt til deg med brukernavn og passord. Logg p&#229; for &#229; registrere fornyelse av medlemskap.");
      } else {
        notify("Det oppstod en feil under sending av epost. Vennligst kontakt " . "<a href=\"mailto:support@studentersamfundet.no\">webansvarlig</a>.");
      }
    } else {
      notify("Ingen bruker er registrert p&#229; epostadressen du oppga. Bruk skjemaet under for &#229; registrere deg.");
      $GLOBALS['extraScriptParams']['page'] = 'register-user';
    }
  }

  public function _logIn() {
    $conn = db_connect();

    $sql = sprintf("SELECT id, passwordReset FROM din_user " .
    "WHERE username = %s " .
    "AND (password = PASSWORD(%s) or password = old_password(%s))", $conn->quoteSmart(scriptParam('username')), $conn->quoteSmart(scriptParam('password')), $conn->quoteSmart(scriptParam('password')));

    $result = $conn->query($sql);
    if (DB :: isError($result) != true) {
      if ($result->numRows() > 0) {
        $row = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $_SESSION['valid-user'] = $row['id'];
        
	// SHA1	
        if ($_GET['debug']){
               echo "{SHA}" . base64_encode(pack("H", hash('sha1', $conn->quoteSmart(scriptParam('password')))));
		
		die("Jeg skammer meg over at dette er skrevet i vim - Sjur");
        }

	if ($row['passwordReset'] == 1) {
          $GLOBALS['extraScriptParams']['page'] = "reset-password";
        }
      } else {
	try
	{
		$sql = sprintf("SELECT id FROM din_user WHERE username = %s", $conn->quoteSmart(scriptParam('username')));
		$result = $conn->query($sql);
		$userExists = $result->numRows() > 0;
		
		$error = true;
		$passwordExists = scriptParam('password') != '';
		
		if($userExists)
		{
			$error = "user exists, wrong password?";
		}
		else
		{
			$error = "user not found..";
		}
	
		$sql = sprintf("INSERT INTO `inside_auth_log`(`username`, `password`,`error`) VALUES(%s,%s,%s)",$conn->quoteSmart(scriptParam('username')), $passwordExists, $error);
	}
	catch(Exception $e)
	{
		mail('komans@studentersamfundet.no','error with errorlog','error with errorlog');
	}	
        notify("Problemer med innlogging.");
      }
    }
  }

  public function _logOut() {
    unset ($_SESSION['valid-user']);
    session_destroy();
    notify("Du er n&#229; logget ut. Logg inn p&#229; nytt eller g&#229; videre til <a href=\"http://www.studentersamfundet.no/\">Studentersamfundets forside</a>.");
  }

  public function _registerJob() {
    $job = new Job(NULL, $_REQUEST);
    $job->store();
  }

  public function _updateJob() {
    $jobId = scriptParam("jobid");
    $job = new JOb($jobId, $_REQUEST);
    $job->store();
  }

  public function _deleteJob() {
    Job :: delete(scriptParam("jobid"));
  }

  public function _registerBarShift() {
    $barshift = new BarShift(NULL, $_REQUEST);
    $barshift->store();
  }

  public function _updateBarShift() {
    $barshiftid = scriptParam("barshiftid");
    $barshift = new barshift($barshiftid, $_REQUEST);
    $barshift->store();
  }

  public function _repeatBarShift() {
    $barshiftid = scriptParam("barshiftid");
    $barshift = new BarShift($barshiftid);
    $barshift->repeat($_REQUEST);
  }

  public function _copyBarShift() {
    $barshiftid = scriptParam("barshiftid");
    $barshift = new BarShift($barshiftid);
    $barshift->copy($_REQUEST);
  }

  public function _deleteBarShift() {
    BarShift :: delete(scriptParam("barshiftid"));
  }

  public function _registerBarShiftWorker() {
    $barshift_id = scriptParam("barshiftid");
    $user_id = scriptParam("userid");
    $bsw = new BarShiftWorker($barshift_id, $user_id);
    $bsw->store();
  }

  public function _registerBarShiftWorkerSelf() {
    $barshift_id = scriptParam("barshiftid");
    $user_id = getCurrentUser();
    $GLOBALS['extraScriptParams']['action'] = "register-barshiftworker";
    $bsw = new BarShiftWorker($barshift_id, $user_id);
    $bsw->store();
  }

  public function _deleteBarShiftWorker() {
    BarShiftWorker :: delete(scriptParam("barshiftid"), scriptParam("userid"));
  }
  public function _registerDivision() {
    $division = new Division(NULL, $_REQUEST);
    $division->store();
  }

  public function _updateDivision() {
    $divisionId = scriptParam("divisionid");
    $division = new Division($divisionId, $_REQUEST);
    $division->store();
  }

  public function _deleteDivision() {
    Division :: delete(scriptParam("divisionid"));
  }

  public function _registerPosition() {
    $position = new Position(NULL, $_REQUEST);
    $position->store();
  }

  public function _updatePosition() {
    $positionId = scriptParam("positionid");
    $position = new Position($positionId, $_REQUEST);
    $position->store();
  }

  public function _deletePosition() {
    Position :: delete(scriptParam("positionid"));
  }

  public function _registerEvent() {
    $event = new Event(NULL, $_REQUEST);
    $event->store();
  }

  public function _updateEvent() {
    $eventId = scriptParam("eventid");
    $event = new Event($eventId, $_REQUEST);
    $event->store();
  }

  public function _repeatEvent() {
    $eventId = scriptParam("eventid");
    $event = new Event($eventId);
    $event->repeat($_REQUEST);
  }

  public function _copyEvent() {
    $eventId = scriptParam("eventid");
    $event = new Event($eventId);
    $event->copy($_REQUEST);
  }

  public function _deleteEvent() {
    Event :: delete(scriptParam("eventid"));
  }

  public function _registerEventComment() {
    $eventComment = new EventComment(NULL, $_REQUEST);
    $eventComment->store();
  }

  public function _updateEventComment() {
    $eventCommentId = scriptParam("eventcommentid");
    $eventComment = new EventComment($eventCommentId, $_REQUEST);
    $eventComment->store();
  }

  public function _deleteEventComment() {
    EventComment :: delete(scriptParam("eventcommentid"));
  }

  public function _registerConcert() {
    $concert = new Concert(NULL, $_REQUEST);
    $concert->store();
  }

  public function _updateConcert() {
    $concertId = scriptParam("concertid");
    $concert = new Concert($concertId, $_REQUEST);
    $concert->store();
  }

  public function _registerConcertReport() {
    $report = new ConcertReport(NULL, $_REQUEST);
    $report->store();
  }

  public function _updateConcertReport() {
    $reportId = scriptParam("concertreportid");
    $report = new ConcertReport($reportId, $_REQUEST);
    $report->store();
  }

  public function _repeatConcert() {
    $concertId = scriptParam("concertid");
    $concert = new Concert($concertId);
    $concert->repeat($_REQUEST);
  }

  public function _copyConcert() {
    $concertId = scriptParam("concertid");
    $concert = new Concert($concertId);
    $concert->copy($_REQUEST);
  }

  public function _deleteConcert() {
    Concert :: delete(scriptParam("concertid"));
  }

  public function _registerWeekProgram() {
    $prog = new WeekProgram(NULL, $_REQUEST);
    $prog->store();
  }

  public function _registerProgramSelection() {
    $prog = new ProgramSelection(NULL, $_REQUEST);
    $prog->store();
  }

  public function _updateProgramSelection() {
    $id = scriptParam("programselectionid");
    $prog = new ProgramSelection($id, $_REQUEST);
    $prog->store();
  }

  public function _uploadDocument() {
    $document = new Document();
    $document->store($_FILES);
  }

  public function _updateDocument() {
    Document :: update($_REQUEST);
  }

  public function _deleteDocument() {
    Document :: delete(scriptParam("documentid"));
  }

  public function _registerUser() {
    $user = new User(NULL, $_REQUEST);
    if ($user->id != -1) {
      if ($user->store()) {
        if (isset ($_REQUEST['nordea'])) {
          $lead = Array (
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email,
            "phonenumber" => $user->phonenumber
          );
          $nordea = new Nordea(NULL, $lead);
          $nordea->store();
        }

        return true;
      }
    }
    $GLOBALS['extraScriptParams']['page'] = "register-user";
    return false;
  }

  public function _updateUser($onlyCurrentUser = false) {
    $userId = scriptParam("userid");
    if ($onlyCurrentUser == true) {
      if ($userId != getCurrentUser()) {
        notify("Du har ikke tilgang til &#229; endre denne brukeren.");
        return false;
      }
    }
    $user = new User($userId, $_REQUEST);
    $user->store();
    
    // check if membercard has been set to no 
    if (!$user->getCardProduced()) {
      if ($user->getLastSticker()) {
        // unset sticker
        $user->setCardSticker(0);
      }
      if ($user->getCardDelivered()) {
        // unset card delivered
        $user->setCardDelivered(0);
      }
    }
  }

  public function _deleteUser() {
    User :: delete(scriptParam('userid'));
  }

  public function _changeUsername($onlyCurrentUser = false) {
    $userId = scriptParam("userid");
    if ($onlyCurrentUser == true) {
      if ($userId != getCurrentUser()) {
        notify("Du har ikke tilgang til &#229; endre denne brukeren.");
        return false;
      }
    }
    $user = new User($userId);
    $user->changeUsername(scriptParam("username"), scriptParam("password"));
  }

  public function _registerMembership() {
    $cardno = scriptParam("cardno");
    $password = scriptParam("verificationCode");
    
    $msa_code = $this->verifyActivationCode($cardno, $password);
    // was the verification successful ?
    if (!is_null($msa_code)) {
      $user = new User(scriptParam("userid"));
      if ($user->getCardProduced()) {
        // user has a card, only renew membership
        return $this->_renewMembership();
      }
      
      if ($user->registerMembership($cardno)) {
        // mark activation code as used
        //$msa_code->setUserId($user->getId());
        //$msa_code->setUsed(new DateTime());
        //$msa_code->store();
        
        $user->sendCardOrderedNotifyMail();
        notify("Betalt medlemskap er registrert. Medlemskort vil bli produsert.");
      } else {
        $GLOBALS['extraScriptParams']['page'] = "register-membership";
      }
    } else {
      notify("Aktiveringsnummer og kode passer ikke sammen. Kortnummeret er det som st&#229;r p&#229; kodelappen, ikke det som st&#229;r p&#229; medlemskortet ditt. Vi gj&#248;r ogs&#229; oppmerksom p&#229; det skilles mellom store og sm&#229; bokstaver.");
      $GLOBALS['extraScriptParams']['page'] = "register-membership";
      $GLOBALS['extraScriptParams']['report-bug'] = "register-membership";
    }
  }

  public function _registerMembershipPayex() {
    $payment = new Payment(NULL, $_POST);
    if ($payment->status == "FAILURE") {
      $GLOBALS['extraScriptParams']['page'] = "register-membership";
    } else {
      $user = new User(scriptParam("userid"));
      
      // check if user needs a member card
      if (is_null($user->getMemberCard())) {
        if (!$user->registerMembershipPayex()) {
          return false;
        }
        $user->sendCardOrderedNotifyMail();
      } else {
        if (!$user->renewMembershipPayex()) {
          return false;
        }
        $user->sendRenewedMembershipRegisteredNotifyMail();
      }
      
      $GLOBALS['extraScriptParams']['page'] = "register-payex-transaction-confirm";
      $GLOBALS['extraScriptParams']['transactionid'] = $payment->transaction_id;
    }
  }

  public function _renewMembership() {
    $id = scriptParam("cardno");
    $password = scriptParam("verificationCode");
    
    if (empty($id)) {
      notify("Du m&#229; taste inn et aktiveringsnummer.");
      $GLOBALS['extraScriptParams']['page'] = "renew-membership";
      return false;
    }
    
    $msa_code = $this->verifyActivationCode($id, $password);
    
    // was the verification successful ?
    if (!is_null($msa_code)) {
      $user = new User(scriptParam("userid"));
      //if ($user->renewMembership()) {
      if ($user->renewMembership($id)) {
        // mark code as used
        //$msa_code->setUsed(new DateTime());
        //$msa_code->setUserId($user->getId());
        //$msa_code->store();
        
        $user->sendRenewedMembershipRegisteredNotifyMail();
        notify("Fornyelse av medlemskapet ditt er registrert. Ny oblat kan hentes i billettluka på studentersamfundet.");
      } else {
        $GLOBALS['extraScriptParams']['page'] = "renew-membership";
      }
    } else {
      notify("Feil aktiveringskode. Kontroller koden og fors&#248;k igjen. Vi gj&#248;r oppmerksom p&#229; at det er forskjell p&#229; store og sm&#229; bokstaver.");
      $GLOBALS['extraScriptParams']['page'] = "renew-membership";
      $GLOBALS['extraScriptParams']['report-bug'] = "renew-membership";
    }
  }
  
  protected function verifyActivationCode($id, $password) {
/*    $msa_code = new MembershipActivationCode();
    if ($msa_code->findById($id)) {
      if (!is_null($msa_code->getUsed())) {
        // code has been used allready, and is therefore illegal
        return null;
      }
    
      if (trim($password) == $msa_code->getCode()) {
        // valid password, return object
        return $msa_code;
      }
    }
    */
    if (checkPassword($id, $password)) {
      return true;
    }
    
    return null;
  }

  public function _cartCheckout() {
    $order_id = scriptParam("order_id");
		$order = new Order($order_id);
		if ($order->user_id != getCurrentUser()) {
			notify("Ugyldig ordrenummer. G&#229; tilbake og pr&#248;v igjen.");
			return;
		}

    $payment = new Payment();
    $payment->executeRedirect(scriptParam('transaction_id_string'), $order_id);
  }

  public function _transactionReturn() {
    $orderRef = scriptParam('orderRef');
    $transaction_id = scriptParam('transactionid');
    $transaction = new Transaction($transaction_id);
    $payment = new Payment();
    if ($payment->completeTransaction($orderRef, $transaction_id)) {
      $order_id = $transaction->order_id;
      $order = new Order($order_id);
      $order->setStatus(4);
      $order->performOperations();
      $GLOBALS['extraScriptParams']['page'] = "transaction-confirmation";
      $GLOBALS['extraScriptParams']['transactionid'] = $transaction_id;
    }
  }

  public function _payexTransaction() {
    $payment = new Payment(NULL, $_POST);
    if ($payment->status == "FAILURE") {
      //notify("failure");
    } else {
      //notify("success");
      $GLOBALS['extraScriptParams']['page'] = "transaction-confirmation";
      $GLOBALS['extraScriptParams']['transactionid'] = $payment->transaction_id;
    }
  }

  public function _payexTestTransaction() {
    $payment = new Payment(NULL, $_POST);
  }

  public function _cartAddProduct() {
    $user = new User(getCurrentUser());
    $order_id = $user->getOrderId();
    if ($order_id == NULL) {
      $order = new Order();
      $order->store();
      //$GLOBALS['extraScriptParams']["order_id"] = $order->id;
      //@ setcookie(getCurrentUser() . "[order_id]", $order->id);
    } else {
      $order = new Order($order_id);
    }
    if ($order->addOrderItem($_POST)) {
      notify("Produkt lagt i handlekurv.");
    }
  }

  public function _updateCart() {
    $keys = array_keys($_POST);
    foreach ($keys as $k) {
      if (strstr($k, "orderitem")) {
        $item_id = substr($k, 9);
        $qty = $_POST[$k];
        if ($qty == 0) {
          OrderItem :: delete($item_id);
        } else {
          $item = new OrderItem($item_id);
          $item->setQuantity($qty);

          $comment = scriptParam("ordercomment$item_id");
          $item->setComment($comment);
        }
      }
    }
    notify("Handlekurven er oppdatert.");
  }

  public function _deleteOrder() {
    Order :: delete(scriptParam("order_id"));
  }

  public function _grantCardno() {
    //$user = new User(scriptParam("userid"));
    //$user->grantCardno();
    notify("Error: ActionParser::_grantCardno() is a deprecated function.");
  }

  public function _updateUserHasCard() {
    //$user = new User(scriptParam("userid"));
    //$user->setHasCard(1);
    notify("Error: ActionParser::_updateUserHasCard() is a deprecated function.");
  }

  public function _updateUserDivisionRequest() {
    $user = new User(scriptParam("userid"));
    $user->updateDivisionRequest(scriptParam("divrequest"));
  }

  public function _updateUserExpiry() {
    $user = new User(scriptParam("userid"));
    $user->updateExpiry(scriptParam("newExpiryDate"));
  }

  public function _updateUserLastSticker() {
    $user = new User(scriptParam("userid"));
    $user->updateLastSticker(scriptParam("newExpiryDate"));
  }

  public function _registerGroup() {
    $groupId = scriptParam("groupid");
    $group = new Group($groupId, $_REQUEST);
    $group->store();
  }

  public function _updateGroup() {
    $groupId = scriptParam("groupid");
    $group = new Group($groupId, $_REQUEST);
    $group->store($_POST);
  }

  public function _deleteGroup() {
    Group :: delete(scriptParam("groupid"));
  }

  public function _registerAction() {
    $actionId = scriptParam("actionid");
    $action = new Action($actionId, $_REQUEST);
    $action->store();
  }

  public function _updateAction() {
    $actionId = scriptParam("actionid");
    $action = new Action($actionId, $_REQUEST);
    $action->store();
  }

  public function _deleteAction() {
    Action :: delete(scriptParam("actionid"));
  }

  public function _registerActionGroupRelationship() {
    $actionId = scriptParam("actionid");
    $groupId = scriptParam("groupid");
    $rel = new ActionGroupRelationship($actionId, $groupId);
    $rel->store();
  }

  public function _registerUserGroupRelationship() {
    $userId = scriptParam("userid");
    $groupId = scriptParam("groupid");
    $users = scriptParam("users");
    if (!empty ($users)) {
      $users = explode("\n", $users);
      foreach ($users as $u) {
        if (is_numeric(trim($u))) {
          $userId = getUseridFromCardno($u);
        } else {
          $userId = getUseridFromEmail(trim($u));
        }
        if ($userId != false) {
          $rel = new UserGroupRelationship($userId, $groupId);
          $rel->store();
        }
      }
    } else
      if (!empty ($userId)) {
        $rel = new UserGroupRelationship($userId, $groupId);
        $rel->store();
      }
  }

  public function _registerArticle() {
    $article = new Article(NULL, $_REQUEST);
    $article->store();
  }

  public function _updateArticle() {
    $articleId = scriptParam("articleid");
    $article = new Article($articleId, $_REQUEST);
    $article->store();
  }

  public function _deleteArticle() {
    Article :: delete(scriptParam("articleid"));
  }

  public function _deleteUserGroupRelationship() {
    UserGroupRelationship :: delete(scriptParam("userid"), scriptParam("groupid"));
  }

  public function _switchFormtype() {
    if (isset ($_SESSION['formtype'])) {
      $_SESSION['formtype'] = ($_SESSION['formtype'] == "fieldset") ? "table" : "fieldset";
    } else {
      $_SESSION['formtype'] = "table";
    }
    $url = $_SERVER['HTTP_REFERER'];

    $pos = strpos($url, "action");
    if ($pos === false) {
      header("Location: $url");
    }
  }

  public function _switchTinyMCETheme() {
    if (isset ($_SESSION['tinyMCE']['theme'])) {
      $_SESSION['tinyMCE']['theme'] = ($_SESSION['tinyMCE']['theme'] == "simple") ? "advanced" : "simple";
    } else {
      $_SESSION['tinyMCE']['theme'] = "simple";
    }
    $url = $_SERVER['HTTP_REFERER'];

    $pos = strpos($url, "action");
    if ($pos === false) {
      header("Location: $url");
    }
  }
  public function _registerDocumentCategory() {
    $docCat = new DocumentCategory(NULL, $_REQUEST);
    $docCat->store();
  }

  public function _updateDocumentCategory() {
    $documentCategory = new DocumentCategory(scriptParam("documentcategoryid"), $_REQUEST);
    $documentCategory->store();
  }

  public function _deleteDocumentCategory() {
    DocumentCategory :: delete(scriptParam("documentcategoryid"));
  }

  public function _registerEventCategory() {
    $eventCat = new EventCategory(NULL, $_REQUEST);
    $eventCat->store();
  }

  public function _updateEventCategory() {
    $eventCategory = new EventCategory(scriptParam("eventcategoryid"), $_REQUEST);
    $eventCategory->store();
  }

  public function _deleteEventCategory() {
    EventCategory :: delete(scriptParam("eventcategoryid"));
  }

  public function _registerJobCategory() {
    $jobCat = new JobCategory(NULL, $_REQUEST);
    $jobCat->store();
  }

  public function _updateJobCategory() {
    $jobCategory = new JobCategory(scriptParam("jobcategoryid"), $_REQUEST);
    $jobCategory->store();
  }

  public function _deleteJobCategory() {
    JobCategory :: delete(scriptParam("jobcategoryid"));
  }

  public function _registerProduct() {
    $product = new Product(NULL, $_REQUEST);
    $product->store();
  }

  public function _updateProduct() {
    $product = new Product(scriptParam("productid"), $_REQUEST);
    $product->store();
  }

  public function _deleteProduct() {
    Product :: delete(scriptParam("productid"));
  }

  public function _registerBugReport() {
    $bug = new BugReport(NULL, $_REQUEST);
    $bug->store();
  }

  public function _updateBugReportStatus() {
    $bug = new BugReport(scriptParam("bugreportid"), NULL);
    $bug->setStatus($_REQUEST);
  }

  public function _deleteBugReport() {
    BugReport :: delete(scriptParam("bugreportid"));
  }

  public function _membershipSale() {
    $userid = scriptParam("userid");

    if (!empty($userid)) {
      $user = new User(scriptParam("userid"));
      $subaction = scriptParam("subaction");
      if ($subaction == "sticker-sale") {
        $user->_registerUpdate("Oblat solgt i billettbod");
        $user->updateExpiry(scriptParam("new-sticker-date"));
        $user->updateLastSticker(scriptParam("new-sticker-date"));
      } elseif ($subaction == "give-sticker") {
        $user->_registerUpdate("Gyldig medlemskap, oblat gitt ut i billettluka");
        $user->updateLastSticker(scriptParam("new-sticker-date"));
      } elseif ($subaction == "give-card") {
        $user->_registerUpdate("Medlemskort utlevert i billettluka");
        $user->setCardDelivered(true);
        $user->updateLastSticker(scriptParam("new-sticker-date"));
      } elseif ($subaction == "order-new-card") {
        $user->_registerUpdate("Nytt medlemskort bestillt via billettluka");
        $user->setCardProduced(false);
        $user->setCardDelivered(false);
        $user->updateLastSticker(0);
      }
      
    } else {
      error("Ukjent bruker-id : " . $userid);
    }

  }

  public function _membercardProduction() {

  }

}
?>
