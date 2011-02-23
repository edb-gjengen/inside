<?php

class Users {
  var $users;
  var $conn;

  function Users(){
    $this->__construct();
  }

  public function __construct(){
    $this->conn = db_connect();
  }

  public function getList($selection = 2, $limit = -1, $search = false, $addressStatus = -1){
    if ($limit > 0){
      $limit = "LIMIT $limit";
    }else {
      $limit = "";
    }
    // If addressstatus != -1, limit to a certain value of valid_address field
    if (!isset($addressStatus)) $addressStatus = -1;
    if ($addressStatus == 10) {
    	$sqladdresslimit = "AND u.valid_address > 0 ";
    } elseif ($addressStatus >= 0) {
		$sqladdresslimit = "AND u.valid_address = $addressStatus ";
	} else {
		$sqladdresslimit = "";
	}

    if ($search != true){
      if ($selection < 0){
      	$sql = "SELECT DISTINCT u.id, concat(u.firstName, ' ', u.lastName) AS name " .
      			"FROM din_user u LEFT JOIN din_usergrouprelationship ugr " .
              	"ON u.id = ugr.user_id " .
              	"AND ugr.group_id = ABS($selection) " .
              	"WHERE ugr.user_id IS NULL " .
				$sqladdresslimit .
              	"ORDER BY name ASC " .
              	"$limit";
      }else {
		$sql = "SELECT DISTINCT u.id, concat(u.firstName, ' ', u.lastName) AS name " .
				"FROM din_user u, din_usergrouprelationship ugr " .
				"WHERE u.id != 0 " .
				"AND u.id = ugr.user_id " .
				"AND ugr.group_id = $selection " .
				$sqladdresslimit .
				"ORDER BY name ASC " .
				"$limit";
      }
    }else {
      $selection = strtoupper($selection);
      if (is_numeric($selection)){
        $sql = "SELECT DISTINCT u.id
                FROM din_user u
                WHERE u.cardno = $selection";
	if(strlen($selection) == 8)
	{
		$sql = "SELECT DISTINCT u.id
                 FROM din_user u left join din_userphonenumber p on u.id = p.user_id
                 WHERE p.number = $selection";

	}
      }else {
      	// Add wildcards around and between words
      	$selection = "%" . str_replace(" ", "% %", $selection) . "%";

        $sql = "SELECT DISTINCT u.id
                FROM din_user u
                WHERE CONCAT(UPPER(u.firstName), ' ', UPPER(u.lastName)) LIKE '$selection'
                OR CONCAT(UPPER(u.firstName), ' ', UPPER(u.lastName)) LIKE '$selection'
                OR UPPER(u.username) LIKE '$selection'
                OR UPPER(u.email) LIKE '$selection'
				$sqladdresslimit
                ORDER BY u.firstname, u.lastname ASC
                $limit";
      }
    }
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->users = $result;
    }else {
      error($result->toString());
    }
    return $this->users;
  }

  public
  function displayExpiryList($selection = 2, $limit = 25, $expiry = '0000-00-00', $format = "screen"){
    switch ($expiry){
    case 'lifetime':
      $expression = "expires IS NULL";
      break;
    case '0000-00-00':
      $expression = "expires = '0000-00-00'";
      break;
    case 'expired':
      $expression ="expires > '0000-00-00' AND expires < NOW()";
      break;
    case 'valid':
      $expression ="expires > NOW()";
      break;
    case 'no-cardno':
      $expression = "u.cardno IS NULL";
      break;
    case 'card-not-produced':
      $expression = "u.cardProduced = 0 AND u.cardno IS NOT NULL";
      break;
    case 'card-not-delivered':
      $expression = "u.cardProduced = 1 AND u.cardDelivered = 0 AND u.cardno IS NOT NULL";
      break;
    case 'no-sticker':
      $expression = "u.cardDelivered = 1 AND u.expires > NOW() AND u.lastSticker < YEAR(u.expires)";
      break;
    case 'all':
      $expression = '1';
      break;
    }
    $sql = "SELECT u.id
            FROM din_user u, din_usergrouprelationship ugr
            WHERE $expression
            AND u.id = ugr.user_id
            AND ugr.group_id = $selection
            LIMIT $limit";
    $result = $this->conn->query($sql);
    if (DB::isError($result) == true) {
      error($result->toString());
      return false;
    }
    if ($format == "screen"){
      if ($result->numRows() > 0){
?>
      <p>Antall treff: <?php print($result->numRows()); ?></p>
      <table class="sortable" id="userlist">
        <tr>
          <th>id</th>
          <th>kortnr</th>
          <th>fornavn</th>
          <th>etternavn</th>
          <th>medlemskort</th>
          <th>utløpsår</th>
          <th><?php print(($expiry == "no-card")? "mangler medlemskort" : "endre til"); ?></th>
          <th>oblat</th>
          <th>endre til</th>
          <th>registrert</th>
          <th>sist endret</th>
         </tr>
<?php
        while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
          $user = new User($row->id);
          $user->displayExpiryList($expiry);
        }
        print("      </table>");
      }else {
        print("<p>Ingen medlemmer registrert.</p>");
      }
    }else if ($format == "file"){
      if ($result->numRows() > 0) {
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="brukerliste.txt"');
        print "id;cardno;firstname;lastname;street;zipcode;postarea;addressStatus;";
        print "email;phoneno;birthdate;expires;lastSticker;registered;lastUpdate\r\n";
        while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
          $user = new User($row->id);
          $user->writeExpiryListFile($expiry);
        }
         exit;
      } else {
        notify("Ingen medlemmer registrert");
      }
    }
  }

  public function displayExpiryListSearch($search){

      $selection = strtoupper($search);
      if (is_numeric($selection)){
        $sql = "SELECT DISTINCT u.id
                FROM din_user u
                WHERE u.cardno = $selection " .
               "OR u.id = $selection";
      }else {
        $sql = "SELECT DISTINCT u.id
                FROM din_user u
                WHERE CONCAT(UPPER(u.firstName), ' ', UPPER(u.lastName), ' ', UPPER(u.username), ' ', UPPER(u.email)) LIKE '%$selection%'
                ORDER BY u.firstname, u.lastname ASC";
      }
    $result = $this->conn->query($sql);
    if (DB::isError($result) == true){
      error($result->toString());
      return false;
    }
    if ($result->numRows() > 0){
?>
      <p>Antall treff: <?php print($result->numRows()); ?></p>
      <table class="sortable" id="userlist">
        <tr>
          <th>id</th>
          <th>kortnr</th>
          <th>fornavn</th>
          <th>etternavn</th>
          <th>medlemskort</th>
          <th>utløpsår</th>
          <th>endre til</th>
          <th>oblat</th>
          <th>endre til</th>
          <th>registrert</th>
          <th>sist endret</th>
        </tr>
<?php
        while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
          $user = new User($row->id);
          $user->displayExpiryList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen medlemmer registrert.</p>");
    }
  }


  public function displayList($selection = 2, $limit = 25, $search = false, $addressStatus = -1){
    $this->getList($selection, $limit, $search, $addressStatus);
    if ($this->users->numRows() > 0){
?>
      <p>Antall treff: <?php print($this->users->numRows()); ?></p>
      <table class="sortable" id="userlist">
        <tr>
          <th>#</th>
          <th>medlem</th>
          <th>fornavn</th>
          <th>etternavn</th>
          <th>epost</th>
          <th>telefon</th>
          <th>grupper</th>
        </tr>
<?php
        while ($row =& $this->users->fetchRow(DB_FETCHMODE_OBJECT)){
          $user = new User($row->id);
          $user->displayList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen medlemmer funnet innenfor valgte søk.</p>");
    }
  }

  public function getDivisionRequestList(){
    if (isAdmin()){
    $sql = "SELECT d.id " .
           "FROM din_user d, din_division dd " .
           "WHERE d.division_id_request IS NOT NULL " .
           "AND dd.id=d.division_id_request";
    }else {
      $currentUser = getCurrentUser();
      $sql = "SELECT udr.id
              FROM din_group g, din_user u, din_usergrouprelationship ugr, din_division d, din_user udr
              WHERE u.id = ugr.user_id
AND ugr.group_id = g.id
AND u.id = $currentUser
AND g.admin = 1
AND g.division_id = d.id
AND udr.division_id_request = d.id";
    }
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->users = $result;
    }else {
      error($result->toString());
    }
    return $this->users;
  }


  public function displayDivisionRequests(){
    $this->getDivisionRequestList();
    if ($this->users->numRows() > 0){
?>
      <p>Disse medlemmene har haket av for at de var aktive i din forening da de registrerte seg. Sjekk om dette stemmer. Dersom du ikke tror de er aktive kan du gjerne kontakte dem og høre om de vil bli aktive!</p>
      <p>Antall treff: <?php print($this->users->numRows()); ?></p>
      <table class="sortable" id="divreqlist">
        <tr>
          <th>fornavn</th>
          <th>etternavn</th>
          <th>epost</th>
          <th>forening</th>
          <th>bruker registrert</th>
          <th></th>
        </tr>
<?php
        while ($row =& $this->users->fetchRow(DB_FETCHMODE_OBJECT)){
          $user = new User($row->id);
          $user->displayDivReqList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen forespørseler registrert.</p>");
    }
  }

  /**
   * Get number of users with study place affiliations
   *
   * @param none
   * @return mixed array
   **/
  public function getUsersStudyPlaceList($year = null) {
    if ($year) {
      $user = new User(1);
      $sql = "SELECT s.navn AS studyPlace, COUNT(*) AS count FROM din_user u INNER JOIN studiesteder s ON u.placeOfStudy=s.id WHERE u.expires='" . $user->getExpiryDate() . "' GROUP BY placeOfStudy ORDER BY studyPlace";
    } else {
      $sql = "SELECT s.navn AS studyPlace, COUNT(*) AS count FROM din_user u INNER JOIN studiesteder s ON u.placeOfStudy=s.id GROUP BY placeOfStudy ORDER BY studyPlace";
    }

  	$result = $this->conn->query($sql);
    if (DB::isError($result) != true) {
      $this->usersStudyPlaceList = $result;
    } else {
      error($result->toString());
    }
    return $this->usersStudyPlaceList;
  }

  /**
   * Get max number of users for one study place affiliations
   *
   * @param none
   * @return mixed array
   **/
  public function getMaxUsersStudyPlace($year = null) {
    if ($year) {
      $user = new User(1);
  	  $sql = "SELECT COUNT(*) AS count FROM din_user WHERE expires='" . $user->getExpiryDate() . "' GROUP BY placeOfStudy ORDER BY count DESC LIMIT 1";
    } else {
  	  $sql = "SELECT COUNT(*) AS count FROM din_user GROUP BY placeOfStudy ORDER BY count DESC LIMIT 1";
    }

  	$result = $this->conn->query($sql);
    if (DB::isError($result) != true) {
      $this->usersStudyPlaceMax = $result->fetchRow(DB_FETCHMODE_OBJECT);
      $this->usersStudyPlaceMax = $this->usersStudyPlaceMax->count;
    } else {
      error($result->toString());
    }
    return $this->usersStudyPlaceMax;
  }

  /**
   * List number of users with study place affiliations
   *
   * @param none
   * @return none
   **/
  public function displayUsersStudyPlaceList() {
    if (isset($_GET['year'])) $year = $_GET['year'];
    else $year = null;
    if (!isset($this->usersStudyPlaceList)) $this->getUsersStudyPlaceList($year);

    print "<h2>Oversikt over hvor medlemmene har registrert at de studerer</h2>\n";

    print "<p>";
    print "<a href=\"" . $_SERVER["PHP_SELF"] . "?page=" . $_GET["page"] . "\">Vis alle</a> | ";
    print "<a href=\"" . $_SERVER["PHP_SELF"] . "?page=" . $_GET["page"] . "&year=current\">inneværende år</a>";
    print "</p>";

    if ($year) {
      print "<p>Viser oversikt over studiestuder for alle som har betalt medlemskap i nåværende år.</p>";
    } else {
      print "<p>Viser alle medlemmer som noensinne har registrert seg.</p>";
    }

    $n = 0;
    if ($this->usersStudyPlaceList->numRows() > 0) {
      $scalefactor = 600/($this->getMaxUsersStudyPlace($year));
      print "<table class=\"sortable\" id=\"usersstudyplacelist\">";
      print "<tr>";
      print "<th>Studiested</th>";
      print "<th colspan=\"2\">Antall</th>";
      print "</tr>\n";

      while ($row =& $this->usersStudyPlaceList->fetchRow(DB_FETCHMODE_OBJECT)) {
        print "<tr>";
        print "<td>" . $row->studyPlace . "</td>";
        print "<td align=\"right\">" . $row->count . "</td>";
        print "<td><img src=\"graphics/bar.png\" height=\"10\" width=\"" . ($row->count)*$scalefactor . "\"></td>";
        print "</tr>\n";
        $n += $row->count;
      }
      print "<tr><td><b>Sum</b></td><td>$n</td><td></td></tr>\n";
      print "</table>";
    } else {
      print "<p>Det er ingen registrerte medlemmer i valgt periode.</p>";
    }
  }

  /**
   * Import membership payments from file recieved from our bank
   **/
  public function parseMembershipPaymentsFromBank ($bankfile) {
    $payment_lines = split("\n", $bankfile);

    $i = 1;
    $n = 0;

    if (isset($_POST["parseonly"])) {
        print "<h3>Resultater av fil-analysen</h3>\n";
        print "<p>For å registrere medlemskapene i databasen, gå nederst på denne siden.</p>\n";
    }

    // read each line
    foreach ($payment_lines as $payment_line) {
      $payment_line = rtrim($payment_line);

      if (substr($payment_line,6,2) == "30") {
        // KID betaling
        $n++;
        $payment = array();
        $payment["date"] = mktime(0,0,0, substr($payment_line,17,2),
                                substr($payment_line,15,2),
                                substr($payment_line,19,2));
        $payment["amount"] = intval(substr($payment_line,40,7)) . "," . substr($payment_line,47,2);
        $payment["info"] = substr($payment_line,49,25);
        $payment["zeroes"] = substr($payment_line,49,4);
        $payment["kundenr"] = intval(substr($payment_line,53,10));
        $payment["fakturanr"] = intval(substr($payment_line,63,10));
        $payment["CRC"] = substr($payment_line,73,1);

        $payment_user = new User($payment["kundenr"]);

        if (isset($_POST["parseonly"])) {
            print "Fant KID-betaling på linje " . $i . ". Beløp " . $payment["amount"] . " ble betalt inn " . date("d.m.Y", $payment["date"]) . "<br />\n";
            print "Medlemsnr: " . $payment["kundenr"] . ", ". $payment_user->getName() . ", fakturanr: " . $payment["fakturanr"] . ", kontrollsiffer: " . $payment["CRC"] . ".<br />\n";
            if ($payment_user->isHonorMember()) {
                print "<font color=\"red\">" . $payment_user->getName() . " er livsvarig medlem.</font><br />\n";
            } elseif ($payment_user->isMember()) {
                print "<font color=\"red\">" . $payment_user->getName() . " er allerede medlem. Medlemsskapet vil bli satt til å være gyldig i ett år ekstra.</font><br />\n";
            }
            print "<br />\n";
        } else {
            $payment_user->renewMembershipBankpayment();
        }
      }

      $i++;
    }
    print "<br />Fant totalt $n betalinger i filen.<br />";
    if ($n > 0) {
        if (isset($_POST["parseonly"])) {
            print "<b>Oppdateringen av medlemskapene er ikke lagret.</b> For å lagre oppdateringen må du velge å ikke analysere filen uten å oppdatere medlemskapsdatabasen nederst på denne siden.<br />\n";
        } else {
            print "<b>Databasen er oppdatert med nytt medlemskap på personene.</b><br />\n";
        }
    }
  }

  /**
   * Update addresses from a CSV file recieved through EA
   *
   * @param string eafile
   * @return boolean
   **/
  public function parseEAfile ($eafile) {
    $ealines = split("\n", $eafile);

    // read each line
    foreach ($ealines as $line) {
      $line = rtrim($line);
      //$line = utf8_decode($line);
      //print $line;
      $eaentry = split(";", $line);

      // reset variables
      $address = "";
      $postno = "";
      $city = "";
      $country = "";
      $valid_address = 0;

      if (is_numeric($eaentry[0])) {
        $user = @new User($eaentry[0]);
        if ($user->_retrieveData()) {
          // parse from different address caretgories
          switch ($eaentry[1]) {
            case "GV":
              print "Gate/vei";
              $address = $eaentry[2];
              if (strlen($eaentry[3]) > 0) $address .= " " . ereg_replace("^0*", "", $eaentry[3]);
              if (strlen($eaentry[4]) > 0) $address .= " " . $eaentry[4];
              if (strlen($eaentry[5]) > 0) $address .= ", oppg. " . $eaentry[5];
              $postno = $eaentry[10];
              $valid_address = 1;
              break;
            case "ST":
              print "Stedsnavn";
              $address = $eaentry[7];
              $postno = $eaentry[10];
              $valid_address = 1;
              break;
            case "PB":
              print "Postboks";
              $address = "Pb. " . $eaentry[6];
              if (strlen($eaentry[7]) > 0) $address .= " " . $eaentry[7];
              $postno = $eaentry[10];
              $valid_address = 1;
              break;
            case "SB":
              print "Serviceboks";
              $address = "Serviceboks " . $eaentry[6];
              if (strlen($eaentry[7]) > 0) $address .= " " . $eaentry[7];
              $postno = $eaentry[10];
              $valid_address = 1;
              break;
            case "PS":
              print "Postnummer";
              $postno = $eaentry[10];
              $valid_address = 1;
              break;
            case "UT":
              print "Utenlandsk adresse";
              $address = $eaentry[8];
              if (strlen($eaentry[9]) > 0) $address .= "\n" . $eaentry[9];
              $postno = $eaentry[10];
              $city = $eaentry[11];
              $country = $eaentry[12];
              $valid_address = 1;
              break;
            default:
              print "Retur: " . $eaentry[13];
              // mark address as invalid;
              $valid_address = 0;
              break;
          }

          // update address if new address data exists
          if (strlen($address . $postno) > 0) {
            if ($eaentry[1] == "UT") { // foreign address
              $user->_registerUpdate("EA-oppdatering, gammel adresse: " . $user->street . ", " . $user->zipcode . " " . $user->city . ", " . $user->country);
              $user->addresstype = "int";
              $user->street = $address;
              $user->zipcode = $postno;
              $user->city = $city;
              $user->state = "";
              $user->country = $country;
              $user->setAddressStatus($valid_address);
              $user->store();
            } else { // norwegian address
              $user->sendNewAddressMail($address, $postno);
              $user->_registerUpdate("EA-oppdatering, gammel adresse: " . $user->street . ", " . $user->zipcode . " " . $user->postarea);
              $user->addresstype = "no";
              $user->street = $address;
              $user->zipcode = $postno;
              $user->setAddressStatus($valid_address);
              $user->store();
            }
          } else {
            $user->_registerUpdate("EA-oppdatering, gammel adresse: " . $user->street . ", " . $user->zipcode . " " . $user->city . " satt som ugyldig");
            $user->setAddressStatus($valid_address);
            $user->_sendWrongAddressMail();
            print "<i>Gammel adresse markert ugyldig.</i><br />\n";
          }
        } else {
          error("Ugyldig bruker-id " . $eaentry[0] . ", addressen ble ikke importert");
        }
      }
    }
    return false;
  }
}
?>
