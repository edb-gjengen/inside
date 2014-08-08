<?php

class User {

    var $id;
    var $cardno;
    var $username;
    var $firstname;
    var $lastname;
    var $addresstype;
    var $street;
    var $zipcode;

    var $postarea;

    var $city;
    var $state;
    var $country;

    var $phonenumber;
    var $email;
    var $birthdate;
    var $placeOfStudy;

    var $expires;
    var $division_id_request;
    var $group_id;
    var $cardProduced;
    var $cardDelivered;
    var $validAddress;
    var $lastSticker;
    
    var $membershipCard;

    var $conn;

    var $fromSnappOrder;

    function User($id = NULL, $data = NULL) {
        $this->__construct($id, $data);
    }

    public function __construct($id, $data = NULL) {
        $conn = & DB :: connect(getDSN());
        if (DB :: isError($conn)) {
            print ("error: " . $conn->toString());
            exit ();
        } else {
            $this->conn = $conn;
        }

        $action = scriptParam("action");
        $this->id = $id;

        if ($id == NULL) { //New user
            if ($data == NULL) {
                error("User: No data supplied.");
                $this->id = -1;
                return false;
            }

            /* Validate user supplied data. */
            if( !validate_username_length($data['username']) ) {
                notify("Brukernavnet må være mellom 3 og 12 tegn.");
                $this->id = -1;
                return false;
            }
            if( !validate_username_chars($data['username']) ) {
                notify("Brukernavnet kan kun inneholde små bokstaver.");
                $this->id = -1;
                return false;
            }
            $this->username = strtolower(stripcslashes($data['username']));
            if( !validate_password_length($data['password1']) ) {
                notify("Passordet må være minst 8 tegn.");
                $this->id = -1;
                return false;
            }
            if( !validate_password_chars($data['password1']) ) {
                notify("Passordet kan ikke inneholde enkel- eller dobbelfnutt eller bakslask.");
                $this->id = -1;
                return false;
            }
            if ($data['password1'] != $data['password2']) {
                notify("Passordene er ikke like.");
                $this->id = -1;
                return false;
            }
            $this->password = $data['password1'];

            $this->addresstype = isset ($data['addresstype']) ? "int" : "no";

            if (isDate($data['birthdate'])) {
                $this->birthdate = $data['birthdate'];
            } else {
                notify("Ugyldig fødselsdato: " . $data['birthdate']);
                $this->id = -1;
                return false;
            }

            if( $data['phonenumber'] ) {
                $data['phonenumber'] = clean_phonenumber($data['phonenumber']);
            }

            /* No duplicate phone numbers */
            if( getUseridFromPhone($data['phonenumber']) !== false ) {
                notify("Telefonnummeret er allerede registrert på en bruker.");
                $this->id = -1;
                return false;
            }

            if (isset ($data['active'])) {
                $this->division_id_request = $data['division'];
            } else {
                $this->division_id_request = NULL;
            }
            if (isset ($data['group'])) {
                $this->group_id = $data['group_id'];
            } else {
                $this->group_id = NULL;
            }
        } else { //ID set, existing user
            if ($data != NULL) {
                // Update existing user
                $this->addresstype = isset ($data['addresstype']) ? "int" : "no";
            } else {
                //Retrieve data from backend for display or other actions
                if ($data = $this->_retrieveData()) {
                    $this->username = stripcslashes($data['ldap_username']);
                    $this->cardno = $data['cardno'];
                    $this->expires = $data['expires'];
                    $this->division_id_request = $data['division_id_request'];
                    $this->lastSticker = $data['lastSticker'];
                    $this->addresstype = $data['addresstype'];
                } else {
                    // not successful
                    return false;
                }
            }

            $this->birthdate = $data['birthdate'];
            $this->cardProduced = $data['cardProduced'];
            $this->cardDelivered = $data['cardDelivered'];
            $this->validAddress = $data['valid_address'];
        }

        //Common initializations
        $this->firstname = stripcslashes($data['firstname']);
        $this->lastname = stripcslashes($data['lastname']);
        if (empty ($data['street'])) {
            $data['street'] = "-";
        }
        $this->street = stripcslashes($data['street']);
        if (empty ($data['zipcode'])) {
            $data['zipcode'] = "-";
        }
        $this->zipcode = $data['zipcode'];
        if ($this->addresstype == "no") {
            if ($action != "register-user") {
                if (empty ($data['postarea'])) {
                    $data['postarea'] = "-";
                }
                $this->postarea = $data['postarea'];
            }
        } else {
            $this->city = $data['city'];
            $this->state = $data['state'];
            $this->country = $data['country'];
        }
        if ($data['phonenumber'] == "00000000" || $data['phonenumber'] == "") {
            $data['phonenumber'] = "-";
        }
        $this->phonenumber = $data['phonenumber'];
        $this->email = $data['email'];
        $this->placeOfStudy = $data['placeOfStudy'];
        return true;
    }

    public function store() {
        if (!$this->_validate()) {
        return false;
        }
        $action = scriptParam("action");
        if ($action == "register-user") {

      $this->conn->autoCommit(false);
      $this->id = getNextId("din_user");
      $sql = sprintf("INSERT INTO din_user " .
      "  (id, username, password, firstname, " .
      "   lastname, addresstype, email, birthdate, " .
      "   placeOfStudy, division_id_request, ldap_username) " . "VALUES " . "  (%s, %s, PASSWORD(%s), %s, %s, %s, %s, " . "   %s, %s, %s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->username), $this->conn->quoteSmart($this->password), $this->conn->quoteSmart($this->firstname), $this->conn->quoteSmart($this->lastname), $this->conn->quoteSmart($this->addresstype), $this->conn->quoteSmart($this->email), $this->conn->quoteSmart($this->birthdate), $this->conn->quoteSmart($this->placeOfStudy), $this->conn->quoteSmart(($this->division_id_request == NULL) ? NULL : $this->division_id_request), $this->conn->quoteSmart($this->username));
      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        if ($result->getCode() == -5) {
          $err = $this->_findConstraintViolation();
          notify("Denne epostadressen eller brukernavnet er allerede registrert.");
          $GLOBALS['extraScriptParams']['page'] = "register-user"; //Swap page
        } else {
          error("New user: " . $result->toString());
          notify("Problem under registrering av ny bruker.");
        }
        return false;
      }
      if ($this->addresstype == "no") {
        $sql = sprintf("INSERT INTO din_useraddressno " .
        "  (user_id, street, zipcode) " .
        "VALUES " .
        "  (%s, %s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->street), $this->conn->quoteSmart($this->zipcode));
      } else {
        $sql = sprintf("INSERT INTO din_useraddressint " . "  (user_id, street, zipcode, city, state, country) " . "VALUES " . "  (%s, %s, %s, %s, %s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->street), $this->conn->quoteSmart($this->zipcode), $this->conn->quoteSmart($this->city), $this->conn->quoteSmart($this->state), $this->conn->quoteSmart($this->country));
      }

      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        $this->conn->rollback();
        if ($result->getCode() == -3) {
          error("Ugyldig postnummer." . $result->toString());
          notify("Ugyldig postnummer.");
        } else {
          error("New user (address): " . $result->toString());
        }
        return false;
      }

      // Mark address as valid
      $this->setAddressStatus(1);

      $sql = sprintf("INSERT INTO din_userphonenumber (user_id, number) VALUES (%s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->phonenumber));
      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        $this->conn->rollback();
        error("New user (phonenumber): " . $result->toString());
        return false;
      }

      //Group relationships
      $groupRel = new UserGroupRelationship($this->id, 1);
      $groupRel->store();
      if ($this->group_id > 1) {
        $groupRel = new UserGroupRelationship($this->id, $this->group_id);
        $groupRel->store();
      }

      //Register cardnumber as used
      if ($this->cardno != NULL) {
        if (!$this->_registerUsedCardno($this->cardno)) {
          notify("Kortnummeret er allerede registrert.");
          $this->conn->rollback();
          return false;
        }
      }

      //Logging
      $sql = sprintf("INSERT INTO din_userupdate " . "VALUES (NULL, NOW(), %s, %s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart("User registered."), $this->conn->quoteSmart((getCurrentUser() == 0) ? $this->id : getCurrentUser()));
      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        $this->conn->rollback();
        error("New user (userUpdate): " . $result->toString());
        return false;
      } else {
        if ($this->conn->commit()) {
          $GLOBALS['extraScriptParams']['userid'] = $this->id;
          if (loggedIn()) {
            notify("Ny bruker er registrert");
          } else {
            notify("Din bruker er registrert.");
            send_welcome_mail($this);
            $GLOBALS['extraScriptParams']['page'] = "register-membership";
          }
          return true;
        } else {
          $this->conn->rollback();
        }
      }

    } else { //Update
      $this->conn->autoCommit(false);
      $sql = "UPDATE din_user SET " . 
      	"firstname = " . $this->conn->quoteSmart($this->firstname) .
      	", lastname = " . $this->conn->quoteSmart($this->lastname) .
      	", addresstype = " . $this->conn->quoteSmart($this->addresstype) .
      	", email = " . $this->conn->quoteSmart($this->email == "" ? NULL : $this->email) .
      	", birthdate = " . $this->conn->quoteSmart($this->birthdate) .
      	", placeOfStudy = " . $this->conn->quoteSmart($this->placeOfStudy) .
      	", cardProduced = " . $this->conn->quoteSmart($this->cardProduced) .
      	", cardDelivered = " . $this->conn->quoteSmart($this->cardDelivered) .
      	" WHERE id = " . $this->conn->quoteSmart($this->id);
      
      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        error("Update user: " . $result->toString());
        notify("Feil under oppdatering av kontaktinfo.");
        return false;
      }

      // Slett adressen hvis den kun består av en x.
      // Brukeren får da en mail med informasjon om å oppdatere adressen sin.
      if (substr($this->street, 0, 1) == 'x') {
        $link = substr($this->street, 2);
        if ($this->_deleteAddress($link)) {
          notify("Adresse slettet.");
        }
      } else {
        if ($this->addresstype == "no") {
          $sql = sprintf("REPLACE INTO din_useraddressno VALUES (%s, %s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->street), $this->conn->quoteSmart($this->zipcode));
        } else {
          $sql = sprintf("REPLACE INTO din_useraddressint VALUES (%s, %s, %s, %s, %s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->street), $this->conn->quoteSmart($this->zipcode), $this->conn->quoteSmart($this->city), $this->conn->quoteSmart($this->state), $this->conn->quoteSmart($this->country));
        }

        $result = $this->conn->query($sql);
        if (DB :: isError($result) == true) {
          $this->conn->rollback();
          if ($result->getCode() == -3) {
            notify("Ugyldig postnummer! Endringer er ikke lagret.");
          } else {
            notify("Feil med adressen. Endringer er ikke lagret.");
            error("New user (address): " . $result->toString());
          }
          return false;
        }

        // ugly fix ... set address manual if the user is admin
        if (isAdmin()) {
          $this->setAddressStatus($this->validAddress);
        } else {
          // mark address as valid
          $this->setAddressStatus(1);
        }
      }
      $sql = sprintf("REPLACE INTO din_userphonenumber (user_id, number) VALUES (%s, %s)", $this->conn->quoteSmart($this->id), $this->conn->quoteSmart($this->phonenumber));

      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        error("New user (phonenumber): " . $result->toString());
        return false;
      }

      if ($result = $this->conn->commit()) {
        $this->_registerUpdate("Brukerinformasjon oppdatert");
        notify("Brukerinformasjon oppdatert.");
      } else {
        error("User rollback; " . $result->toString());
        $this->conn->rollback();
      }
    }
  }

  public function _validate() {
    $valid = true;
    if( !valid_email($this->email) ) {
      notify("Ugyldig format på epostadresse.");
      $valid = false;
    }
    return $valid;
  }

  public function _retrieveData() {
    $sql = "SELECT u.*, up.number AS phonenumber " . "FROM din_user u LEFT JOIN din_userphonenumber up " . "ON u.id = up.user_id " . "WHERE u.id = $this->id";
    $result = & $this->conn->query($sql);
    if (DB :: isError($result) != true) {
      if ($row = & $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        if ($row['addresstype'] == "no") {
          $sql = "SELECT ua.street, ua.zipcode, pn.poststed AS postarea " . "FROM din_useraddressno ua, postnummer pn " . "WHERE ua.user_id = $this->id " . "AND ua.zipcode = pn.postnummer";
        } else {
          $sql = "SELECT street, zipcode, city, state, country " . "FROM din_useraddressint " . "WHERE user_id = $this->id";
        }
        $result = & $this->conn->query($sql);
        if (DB :: isError($result) != true) {
          if ($row2 = & $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $row = $row + $row2;
          }
          return $row;
        } else {
          error("Users: " . $result->toString());
          return false;
        }
      } else {
        error("Users: invalid userid");
        return false;
      }
    }
  }

    // Fetch the postarea of a given zipcode
    public function getPostarea ($zipcode = null) { // = $this->zipcode) {
        if ($zipcode == null) $zipcode = $this->zipcode;

        $sql = "SELECT poststed AS postarea FROM postnummer WHERE postnummer=\"$zipcode\"";
        $result = & $this->conn->query($sql);
        if (DB :: isError($result) != true) {
            $row = & $result->fetchRow(DB_FETCHMODE_ASSOC);
            return $row["postarea"];
        }
        return false;
    }

  public function getId () {
    return $this->id;
  }

  public function getLastSticker () {
    if ($this->lastSticker > 0) {
      return $this->lastSticker;
    }
    return null;
  }

  public function changeUsername($username, $password) {
      $sql = "UPDATE din_user SET " . "  username = '$username' " . "WHERE " . "  id = $this->id";
      $result = $this->conn->query($sql);

      if (DB :: isError($result) == true) {
          if ($result->getCode() == -5) {
              $err = $this->_findConstraintViolation();
              error("Change username: $err already registered.");
              notify("Brukernavnet er opptatt.");
              $GLOBALS['extraScriptParams']['page'] = "change-username"; //Swap page
          } else {
              error("Change username: " . $result->toString());
              notify("Problem under endring av brukernavn.");
          }
          return false;
      } else {
          $this->_registerUpdate("Brukernavn endret til $username");
          notify("Brukernavn endret.");
      }
  }

  public function getRegistered() {
    $sql = "SELECT uu.date FROM din_user u, din_userupdate uu
                    WHERE uu.user_id_updated=u.id AND u.id = $this->id
                    ORDER BY uu.date ASC LIMIT 1";
    $result = & $this->conn->query($sql);
    if (DB :: isError($result) == true) {
      error("Get user registered: " . $result->toString());
      return false;
    }

    if ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
      return $row->date;
    }

  }

  public function getLastUpdate() {
    $sql = "SELECT uu.date FROM din_user u, din_userupdate uu
                    WHERE uu.user_id_updated=u.id AND u.id = $this->id
                    ORDER BY uu.date DESC LIMIT 1";
    $result = & $this->conn->query($sql);
    if (DB :: isError($result) == true) {
      error("Get user last update: " . $result->toString());
      return false;
    }

    if ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
      return $row->date;
    }
  }

  public function getOrderId() {
    $sql = "SELECT id FROM din_order " .
    			 "WHERE user_id = $this->id " .
    			 "AND order_status_id < 3 " .
    			 "ORDER BY timestamp DESC LIMIT 1";
    $result = $this->conn->query($sql);
    if (DB :: isError($result) == true) {
      return null;
    }

    if ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)) {
      return $row->id;
    }else {
      return null;
    }
  }

	public function getName() {
	  return trim($this->firstname.' '.$this->lastname);
	}

  public function getContactInfo() {
    $c = "$this->firstname $this->lastname\n";
    $c .= "$this->phonenumber\n";
    $c .= "$this->email";
    return $c;
  }

  public function _findConstraintViolation() {
    $sql = "SELECT id FROM din_user WHERE id = $this->id";
    $result = $this->conn->query($sql);
    if ($result->numRows() > 0) {
      return "id";
    }
    $sql = "SELECT id FROM din_user WHERE username = '$this->username'";
    $result = $this->conn->query($sql);
    if ($result->numRows() > 0) {
      return "brukernavnet";
    }
  }

  public function displayGroups() {
    $sql = "SELECT g.id, g.name " . "FROM din_group g, din_usergrouprelationship ugr " . "WHERE ugr.user_id = $this->id " . "AND ugr.group_id = g.id " . "ORDER BY g.name ASC ";
    $result = $this->conn->query($sql);
    if (DB :: isError($result) == true) {
      error("Vis gruppemedlemskap: " . $result->toString());
      notify("Informasjon om gruppemedlemskap utilgjengelig.");
      return false;
    }

    print ("<table>");
    print ("<tr><th colspan=\"2\">Grupper brukeren er medlem av</th></tr>");
    while ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
      print ("<tr><td>$row->name</td>");
      if ($row->id != 1) {
        displayOptionsMenuTable(Array (
          "user_id" => $this->id,
          "group_id" => $row->id
        ), USERGROUPRELATIONSHIP, "usergrouprelationship", "view-edit-options-usergrouprelationship", NULL, "display-user");
      } else {
        print ("<td></td>");
      }
      print ("</tr>");
    }
    print ("</table>");
  }

  public function displayOrders($status = 1) {
    $sql = "SELECT id FROM din_order WHERE user_id = $this->id AND order_status_id = $status";
    $result = & $this->conn->query($sql);
    if (DB :: isError($result) == true) {
      error("Display orders: " . $result->toString());
      return false;
    }

    if ($result->numRows() == 0) {
      print ("<p>Ingen handlekurver funnet");
      return false;
    }
    while ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
      $order = new Order($row->id);
      $edit = true;
      $order->displayShortList($edit);
    }

  }

  public function registerMembership($cardno) {
    // check that the activation code is valid
    $sql = "SELECT id FROM din_user WHERE cardno=$cardno";
    $result = $this->conn->query($sql);
    if (DB :: isError($result) != true) {
      if ($result->numRows() > 0) {
        notify("Kortnummeret er allerede i bruk. Vennligst kontrollér nummeret.");
        return false;
      }
    } else {
      error($result->toString());
      notify("En ukjent feil oppstod. Vennligst kontakt administrator.");
    }
    
    // register cardno as used
    $sql = "INSERT INTO din_usedcardno VALUES ($this->cardno, NOW())";
    $result = $this->conn->query($sql);
    
    if ($this->cardno == null) {
      // user doesn't have a previous card number, set his cardno to current cardno
      $this->cardno = $cardno;
    }
    
    $expires = getNextMembershipExpiryDate();

    $sql = "UPDATE din_user SET cardno=" . $this->cardno . ", expires = '$expires' WHERE id=" . $this->id;
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true) {
      $this->_registerUpdate("Medlemskap registrert.");
      return true;
    } else {
      error($result->toString());
      notify("En ukjent feil oppstod. Vennligst kontakt administrator.");
    }
  }

  function registerMembershipPayex() {
    return $this->grantCardno("payex");
  }

  public function renewMembership($cardno) {
    if (!$this->hasExpired()) {
      notify("Du har allerede gyldig medlemskap for dette året.");
      return false;
    }
    $expires = getNextMembershipExpiryDate();
    $this->conn->autoCommit(false);
    if ($this->_registerUsedCardno($cardno) == true) {
      $sql = "UPDATE din_user SET " . "  expires = '$expires' " . "WHERE " . "  id = $this->id";
      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        error($result->toString());
        return false;
      }
      $this->_registerUpdate("Medlemskap fornyet.");
      if ($result = $this->conn->commit()) {
        return true;
      } else {
        notify("Problemer med registrering av fornyet medlemskap. Ta kontakt med <a href='mailto:support@studentersamfundet.no'>support@studentersamfundet.no</a>.");
        error("Kortnummer: " . $result->toString());
        return false;
      }
    } else {
      notify("En ukjent feil oppstod. Vennligst kontakt administrator.");
    }
  }

  public function renewMembershipPayex() {
    if (!$this->hasExpired()) {
      notify("Du har allerede gyldig medlemskap for dette året.");
      return false;
    }
    $expires = getNextMembershipExpiryDate();
    $this->conn->autoCommit(false);
    $sql = "UPDATE din_user SET " . "  expires = '$expires' " . "WHERE " . "  id = $this->id";
    $result = $this->conn->query($sql);
    if (DB :: isError($result) == true) {
      error($result->toString());
      return false;
    }
    $this->_registerUpdate("Medlemskap fornyet med Payex.");
    if ($result = $this->conn->commit()) {
      notify("Medlemskap fornyet.");
      return true;
    } else {
      notify("Problemer med registrering av fornyet medlemskap. <a href='mailto:support@studentersamfundet.no'>Tilkall hjelp</a>.");
      error("Kortnummer: " . $result->toString());
      return false;
    }
  }

  public function renewMembershipEurobate() {
    if (!$this->hasExpired()) {
      // har allerede gyldig medlemskap
      return false;
    }
    $expires = getNextMembershipExpiryDate();
    $this->conn->autoCommit(false);
    $sql = "UPDATE din_user SET " . "  expires = '$expires' " . "WHERE " . "  id = $this->id";
    $result = $this->conn->query($sql);
    if (DB::isError($result) == true) {
      return false;
    }
    $this->_registerUpdate("Medlemskap fornyet med SMS.");
    if ($result = $this->conn->commit()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Bankbetaling med KID
   */
  public function renewMembershipBankpayment() {
    if (false and $this->isHonorMember()) {
      notify($this->id . ", " . $this->getName() . " er livsvarig medlem. Medlemskapet vil ikke bli oppdater.");
      return false;
    } else {
      $expires = getNextMembershipExpiryDate();
      if ($expires <= $this->expires) {
        $newexpires = date("Y-m-d", strtotime("+1 year", strtotime($expires)));
        notify($this->id . ", " . $this->getName() . " har allerede gyldig medlemskap for dette året. Har utløpsdato " . $this->expires . ", men ny ble prøvd satt til " . $expires . ". Personen vil få satt ny utløpsdato på medlemskapet til " . $newexpires);
        $expires = $newexpires;
      }
      $this->conn->autoCommit(false);
      $sql = "UPDATE din_user SET " . "  expires = '$expires' " . "WHERE " . "  id = $this->id";

      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        error($result->toString());
        return false;
      }

      if ($result = $this->conn->commit()) {
        // send mail to the user
        $this->sendRenewedMembershipRegisteredNotifyMail();

        $this->_registerUpdate("Medlemskap fornyet med KID-betaling til bank.");
        notify($this->id . ", " . $this->getName() . ": medlemskap fornyet fram til " . $expires . ".");
        return true;
      } else {
        notify($this->id . ", " . $this->getName() . ": Problemer med registrering av fornyet medlemskap. <a href='mailto:support@studentersamfundet.no'>Tilkall hjelp</a>.");
        error("Kortnummer: " . $result->toString());
        return false;
      }
    }
  }

  /* Order new membercard if old one is lost */
  public function renewMembercardPayex() {
        if ($this->hasExpired()) {
            notify("Du har ikke gyldig medlemskap for dette året. <a href='https://inside.studentersamfundet.no/index.php?page=register-membership'>Kjøp eller registrer medlemskap.</a>");
            return false;
        }
        $this->conn->autoCommit(false);
        $sql = "UPDATE din_user SET cardProduced='0', cardDelivered='0' WHERE id = " . $this->id;
        $result = $this->conn->query($sql);
        if (DB :: isError($result) == true) {
            error($result->toString());
            return false;
        }
        $this->_registerUpdate("Nytt medlemskort bestilt med Payex.");
        if ($result = $this->conn->commit()) {
            notify("Medlemskortet er lagt i produksjonskøen.");
            return true;
        } else {
            notify("Problemer med registrering av bestillingen.");
            error("Kortnummer: " . $result->toString());
            return false;
        }
  }

  public static function delete($id) {
		/*
		$conn = db_connect();
        $sql = "DELETE FROM din_user WHERE id = $id LIMIT 1";
        $result = $conn->query($sql);
        if (DB :: isError($result) != true) {
            notify("Bruker slettet.");
        } else {
            error($result->toString());
		}
		*/
  }
	
  /*
   * $newDate = YYYY-MM-DD or null (lifetime membership)
   * Requires a full date for expiry, has to end on 1. of august or we get an exception.
   * (orly? tror jeg har kodet noe feil her, men er ikke så viktig med datosjekk selv om
   *  jeg skulle ønske det var der)
   */
  public function updateExpiry($newDate) {
    if($newDate !== null && !preg_match("#^\d{4}-\d{2}-\d{2}$#", $newDate))
    	throw new Exception("Invalid date: $newDate");
  	
  	if($this->expires === $newDate)
  		throw new Exception("Utløpsdatoen har allerede denne verdien");
  	
  	// Logger medlemskapssalget.
  	//$log_entry_id = logExpiryUpdate($this->id, $this->expires, $newDate);
  	
    if ($newDate === null) {
      $sql = "UPDATE din_user SET expires = NULL WHERE id = $this->id LIMIT 1";
    } else {
      $sql = "UPDATE din_user SET expires = '$newDate' WHERE id = $this->id LIMIT 1";
    }
    
    $conn = db_connect();
    $result = $this->conn->query($sql);
    if (DB :: isError($result) != true) {
      $this->_registerUpdate("Utløpsdato oppdatert til " . $newDate);
      notify("Utløpsdato oppdatert.");
    } else {
      // logger at det gikk feil (håper denne funker ;))
      //logExpiryUpdateFail($log_entry_id);
      
      error($result->toString());
    }
  }
  
  function logExpiryUpdate($user_id, $expiry_date, $new_expiry)
  {
  	$timestamp = strftime('%F %T', time());
  	$sql = "INSERT INTO din_membersale_log(`user_id`,`last_expiry`,`new_expiry`,`time`,`failed`) VALUES('{$user_id}', '{$expiry_date}', '{$new_expiry}','{$timestamp}',0)";
  	$result = $this->conn->query($sql);
  	
  	return mysql_insert_id();
  }
  
  function logExpiryUpdateFail($entry_id)
  {
  	$register_fail = "UPDATE din_membersale_log SET `failed`=1 WHERE `id`='{$entry_id}'";
    $this->conn->query($sql);
  }
	/*
	 * $newDate = YYYY | YYYY/YY
	 */
    public function updateLastSticker($newDate) {
    	if(!preg_match('#^\d{4}(/\d{2})?$#', $newDate)) {
		// If not known format, try to convert it:
		$tempDate = strtotime($newDate);
		if (!$tempDate) {
	    		throw new Exception("Invalid date format: $newDate");
		} else {
			// Determine if we want to have YYYY or YYYY/YY:
			$currentYear = (int)date("Y", $tempDate);
			$previousYear = $currentYear - 1;
			$newDate = ((int)date("n", $tempDate) <= 8) ? 
				((string)$previousYear) . "/" . ((string)($currentYear % 100)) :
				(string)$currentYear;
		}
	}
    	
        $sql = "UPDATE din_user SET lastSticker = '$newDate' WHERE id = $this->id LIMIT 1";
        $result = $this->conn->query($sql);
        if (DB :: isError($result) != true) {
            $this->_registerUpdate("Oppdatert oblat til " . $newDate);
            $this->lastSticker = $newDate;
            notify("Oblat oppdatert til " . $this->getLastSticker() . " for " . $this->getName() . ".");

            // Assume that a letter is sent to the registered address.
            // We mark the address as valid, and will update it as invalid if the letter is not delivered
            $this->setAddressStatus(1);
        } else {
            error($result->toString());
        }
    }
    
    // Used when a new card is ordered
    public function unsetLastSticker()
    {
    	$sql = "UPDATE din_user SET lastSticker = '0' WHERE id = $this->id LIMIT 1";
        $result = $this->conn->query($sql);
        if (DB :: isError($result) != true) {
            $this->_registerUpdate("Oppdatert oblat til " . $newDate);
            $this->lastSticker = $newDate;
            notify("Oblat oppdatert til " . $this->getLastSticker() . " for " . $this->getName() . ".");

            // Assume that a letter is sent to the registered address.
            // We mark the address as valid, and will update it as invalid if the letter is not delivered
            $this->setAddressStatus(1);
        } else {
            error($result->toString());
        }
    }

    public function displayList() {
        print "<tr>\n";
        print "  <td class=\"number\">" . $this->cardno . "</td>\n";
        print "  <td class=\"is_member\">";
        if ($this->hasExpired()) {
            print '<img src="graphics/cross.png" alt="nei" />';
        } else {
            print '<img src="graphics/tick.png" alt="ja" />';
        }
        print "</td>\n";

        print "  <td><a href=\"index.php?page=display-user&amp;userid=" .
            $this->id . "\" title=\"mer informasjon om " .
            $this->firstname . " " . $this->lastname . "\">" .
            $this->firstname . "</a></td>\n";

        print "<td>" . $this->lastname . "</td>\n";

        print "<td>" . prepareForHTML($this->email) . "</td>\n";

        print "<td class=\"phone\">";
        print $this->phonenumber;
        if ($this->phonenumber != "-") {
            print "<a href=\"callto://+47" . $this->phonenumber . "\" " .
                "title=\"ring til " . $this->firstname . " " . $this->lastname . "\">";
            print "<img src=\"graphics/call_to.png\" alt=\"ring til " . $this->firstname . " " . $this->lastname . "\" />";
            print "</a>";
        }
        print "</td>\n";

        print "<td>";
        $this->_printGroups(", ");
        print "</td>\n";

        print "</tr>\n";
    }

    public function displayDivReqList() {
        if ($this->division_id_request != NULL) {
            $div = new Division($this->division_id_request);
        } else {
            return false;
        }

        print "<tr>\n";

        print "  <td><a href=\"index.php?page=display-user&amp;userid=" . $this->id . "\" title=\"mer informasjon om " . $this->firstname . " " . $this->lastname . "\">";
        print $this->firstname;
        print "</a></td>\n";

        print "<td>" . $this->lastname . "</td>\n";

        print "<td>" . prepareForHTML($this->email) . "</td>\n";

        print "<td>" . $div->name . "</td>\n";

        print "<td>" . $this->getRegistered() . "</td>\n";

        print "<td>";
        print "<form action=\"index.php?page=display-division-requests&amp;userid=" . $this->id . "\" method=\"post\">";
        print "<div>";
        print "<input type=\"hidden\" name=\"action\" value=\"update-user-division-request\" />";
        print "<select name=\"divrequest\">";
        print "<option value=\"accept\">godkjenn forespørsel</option>";
        print "<option value=\"reject\">avvis forespørsel</option>";
        print "</select>";
        print "<input type=\"submit\" value=\"lagre\" />";
        print "</div>";
        print "</form>";
        print "</td>\n";

        print "</tr>\n";
    }

    public function isMember() {
        if ($this->cardno == NULL) {
            return false;
        } else {
            return true;
        }
    }

    public function isActive() {
        $sql = "SELECT group_id " . "FROM din_usergrouprelationship " . "WHERE user_id = $this->id ";
        $result = $this->conn->query($sql);

        if (DB :: isError($result) == true) {
            error("isActive: " . $result->toString());
            return false;
        }
        if ($result->numRows() > 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getNumProductsBought($product_id = 0) {
        $sql = "SELECT SUM(oi.quantity) AS num FROM din_user u, din_order o, din_order_item oi " .
        "WHERE o.user_id = u.id AND oi.order_id = o.id AND oi.product_id = 2 AND o.order_status_id = 4 AND u.id = $this->id";
        $result = $this->conn->query($sql);

        if (DB :: isError($result) == true) {
            error("isActive: " . $result->toString());
            return 0;
        }
        if ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
            return $row[0];
        } else {
            return false;
        }
    }

    public function hasExpired() {
      if ($this->expires == NULL) {
        return false;
      } else
        if (strtotime($this->expires) > time()) {
          return false;
        } else {
          return true;
        }
    }

    public function getCardProduced() {
      return $this->cardProduced;
    }

    public function setCardProduced($produced) {
      if ($produced) {
        $sql = "UPDATE din_user SET cardProduced=1 WHERE id=$this->id";
        $desc = "medlemskort produsert";
        $this->cardProduced = 1;
      } else {
        $sql = "UPDATE din_user SET cardProduced=0, cardDelivered=0 WHERE id=$this->id";
        $desc = "mangler medlemskort";
        $this->cardProduced = 0;
      }
      $result = $this->conn->query($sql);

      if (DB :: isError($result) != true) {
        $this->_registerUpdate("Medlemskortstatus endret til $desc");
        notify("Medlemskortstatus for <strong>" . $this->firstname . " " . $this->lastname . "</strong> er endret til <strong>" . $desc . "</strong>");
      }
    }

    public function getCardDelivered() {
      return $this->cardDelivered;
    }

    public function setCardDelivered($delivered) {
      if ($delivered) {
        $sql = "UPDATE din_user SET cardDelivered=1 WHERE id=$this->id";
        $desc = "medlemskort utlevert";
        $this->cardDelivered = 1;
      } else {
        $sql = "UPDATE din_user SET cardDelivered=0 WHERE id=$this->id";
        $desc = "medlemskort ikke utlevert";
        $this->cardDelivered = 0;
      }
      $result = $this->conn->query($sql);

      if (DB :: isError($result) != true) {
        $this->_registerUpdate("Medlemskortstatus endret til $desc");
        notify("Medlemskortstatus for <strong>" . $this->firstname . " " . $this->lastname . "</strong> er endret til <strong>" . $desc . "</strong>");
      }
    }

    public function hasCardSticker() {
    	return $this->getLastSticker() != getStickerPeriod($this->expires);
    /*  if ( $this->getLastSticker() != getStickerPeriod($this->expires) ) {
        return true;
      }
      return false;*/
    }
    
	/*public function getNewStickerDate()
	{
		$year = substr($this->getExpiryDate(),0,4);
		return (substr($year,0,4) - 1) . '/' . substr($year,2,2);
	}
*/
    public function getExpiryDate($year = null) {
     return getExpiryDate($year);
    }

    public function _registerUsedCardno($cardno) {
      $sql = "INSERT INTO din_usedcardno VALUES ( " . "  $cardno, NOW())";
      $result = $this->conn->query($sql);
      if (DB :: isError($result) != true) {
        return true;
      } else {
        error("Registrér kortnummer: " . $result->toString());
        notify("Kortnummeret er allerede registrert.");
      }
    }

    public function grantCardno($src = NULL) {
      $sql = "SELECT value FROM din_settings " . "WHERE name = 'next_online_cardno'";
      $result = $this->conn->query($sql);
      if (DB :: isError($result) == true) {
        error("grantCardno: " . $result->toString());
        return false;
      }
      if ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
        $cardno = $row[0];
      }

      $expires = $this->getExpiryDate();
      $sql = "UPDATE din_user SET " . "  cardno = $cardno, " . "  expires = '$expires' " . "WHERE " . "  id = $this->id";
      $result = $this->conn->query($sql);
      if (DB :: isError($result) != true) {
        $this->cardno = $cardno;
        $this->expires = $expires;
        notify("<strong>$this->firstname $this->lastname</strong> er tildelt medlemskortnummer <strong>$cardno</strong>");
        $this->_registerUpdate("Bruker tildelt kortnummer. $src");

        $sql = "UPDATE din_settings SET value = value + 1 WHERE name = 'next_online_cardno'";
        $this->_registerUsedCardno($cardno);
        $result = $this->conn->query($sql);
        return true;
      } else {
        error("Registrér kortnummer: " . $result->toString());
        notify("Problemer med registrering av kortnummer.");
        return false;
      }
    }

    /**
     * changes validity status of users address
     * values can be:
     * 0 - not valid address
     * 1 - valid address
     * 2 - status of address unknown
     *
     * @param integer $value
     * @return boolean
     **/
    public function setAddressStatus($value) {
        // only update if new status is different from old one
        if ($this->validAddress == $value) {
            // do nothing, old and new status is the same
            return true;
        } else {
            // check for valid input value
            if (is_numeric($value) && $value >= 0 && $value <=2) {
            $sql = "UPDATE din_user SET " .
                    "valid_address = '$value' " .
                    "WHERE " .
                    "id = $this->id";
    	        $result = $this->conn->query($sql);
    
                if (DB :: isError($result) != true) {
                    $this->validAddress = $value;
    
                    switch ($value) {
                        case 1:
                            $valuetext = "gyldig"; break;
                        case 2:
                            $valuetext = "ukjent"; break;
                        default:
                            $valuetext = "ugyldig"; break;
                    }
                    // store update
                    $this->_registerUpdate("Adressestatus satt til $valuetext.");
    
                    // done
                    return true;
                } else {
                    notify("Problemer med registreringen av adressestatus");
                    return false;
                }
            } else {
                notify("Ugyldig adressestatusverdi: " . $value);
                return false;
            }
        }
        return false;
    }

    public function getAddressStatus() {
        switch ($this->validAddress) {
            case 1:
                return "gyldig"; break;
            case 2:
                return "ukjent"; break;
            default:
                return "ugyldig"; break;
        }
    }

    public function isHonorMember () {
        if ($this->expires == NULL) return true;
        return false;
    }

    public function setCardno($value) {
        $sql = "UPDATE din_user SET cardno = $value " .
            "WHERE id = $this->id";
        $result = $this->conn->query($sql);

        if (DB :: isError($result) != true) {
            $value = ($value == "NULL") ? "NULL" : $value;
            $this->_registerUpdate("Medlemskortnummer endret til $value");
            notify("Medlemskortnummer for <strong>$this->firstname $this->lastname</strong> er endret til <strong>$value</strong>");
        }
    }

    public function setExpires($value) {
    
    	$this->updateExpiry($value);
    	/*
        $sql = "UPDATE din_user SET expires = '$value' " .
            "WHERE id = $this->id";
        $result = $this->conn->query($sql);

        if (DB :: isError($result) != true) {
            $this->_registerUpdate("Utløpsdato endret til $value");
            notify("Utløpsdato for <strong>$this->firstname $this->lastname</strong> er endret til <strong>$value</strong>");
        }*/
    }

    public function updateDivisionRequest($value) {
        if (empty ($value)) {
            notify("Ugyldig verdi.");
            return false;
        }
        if ($this->division_id_request != NULL) {
            $div = new Division($this->division_id_request);
        }
        $sql = "UPDATE din_user SET " . "  division_id_request = NULL " . "WHERE " . "  id = $this->id";
        $result = $this->conn->query($sql);
        if (DB :: isError($result) != true) {
            if ($value == "reject") {
                $this->_registerUpdate("Aktivforespørsel avvist");
                notify("Forespørsel fra <strong>$this->firstname $this->lastname</strong> om å bli registrert som aktiv i <strong>$div->name</strong> er <strong>avslått</strong>.");
            } else {
                if ($value == "accept") {
                    $ugr = new UserGroupRelationship($this->id, 2);
                    $ugr->store();
                    $group_id = $div->getNoAdminGroup();
                    if ($group_id != NULL) {
                        $ugr2 = new UserGroupRelationship($this->id, $group_id);
                        $ugr2->store();
                    }
                    $this->_registerUpdate("Aktivforespørsel godkjent");
                    notify("Forespørsel fra <strong>$this->firstname $this->lastname</strong> om å bli registrert som aktiv i <strong>$div->name</strong> er <strong>godkjent</strong>.");
                }
            }
        } else {
            error("user-div-req: " . $result->toString());
            notify("Behandling av forespørsel ble ikke utført.");
        }
    }

    public function _printGroups($sep) {
        $sql = "SELECT g.id, g.name " . "FROM din_group g, din_usergrouprelationship ugr " . "WHERE ugr.user_id = $this->id " . "AND ugr.group_id = g.id " . "ORDER BY g.name ASC ";
        $result = $this->conn->query($sql);

        if (DB :: isError($result) != true) {
            if ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
                print ($row->name);
            }
            while ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
                print ($sep . $row->name);
            }
            return true;
        } else {
            error("Vis gruppemedlemskap: " . $result->toString());
            notify("Informasjon om gruppemedlemskap utilgjengelig.");
        }
    }

    public function _deleteAddress($link = null) {
        if ($this->addresstype == 'no') {
            $sql = "DELETE FROM din_useraddressno WHERE user_id = $this->id";
        } else {
            $sql = "DELETE FROM din_useraddressint WHERE user_id = $this->id";
        }
        $result = $this->conn->query($sql);
        if (DB :: isError($result) == true) {
            error("User delete address: " . $result->toString());
            return false;
        } else {
            $this->setAddressStatus(0);
            $this->_sendWrongAddressMail($link);
            $this->_registerUpdate('Adresse slettet.');
            return true;
        }
    }

    public function displayExpiryListSingle() {
        print "<table id=\"userlist\">\n";
        print "  <tr>\n";
        print "    <th>id</th>\n";
        print "    <th>kortnr</th>\n";
        print "    <th>fornavn</th>\n";
        print "    <th>etternavn</th>\n";
        print "    <th>utløpsår</th>\n";
        print "    <th>endre til</th>\n";
        print "    <th>oblat</th>\n";
        print "    <th>endre til</th>\n";
        print "  </tr>\n";

        $this->displayExpiryList();

        print "</table>\n";
    }

    public function writeExpiryListFile($expiry = NULL) {
        print $this->id . ";";
        print $this->cardno . ";";
        print $this->firstname .";";
        print $this->lastname . ";";
        print strip_nl($this->street) . ";";
        print $this->zipcode . ";";
        print $this->postarea . ";";
        print $this->getAddressStatus() . ";";
        print $this->email . ";";
        print $this->phonenumber . ";";
        print $this->birthdate . ";";
        print $this->expires . ";";
        print $this->lastSticker . ";";
        print $this->getRegistered() . ";";
        print $this->getLastUpdate();
        print "\r\n";
    }

    public function displayExpiryList($expiry = NULL) {
        //$card = $this->getMembershipCard();
      
        print "<tr>\n";
        print "  <td class=\"number\">" . $this->id . "</td>\n";
        
        print "  <td class=\"number\" id=\"user_" . $this->id . "_cardno\">";
        //if (!is_null($card)) print $card->getId();
        print $this->cardno;
        print  "</td>\n";
        
        print "  <td><a href=\"index.php?page=display-user&amp;userid=" . $this->id . "\" title=\"mer informasjon om " . $this->firstname . " " . $this->lastname . "\">" . $this->firstname . "</a></td>\n";
        print "  <td>" . $this->lastname . "</td>\n";
        
        print "  <td>";
        if ($this->cardno == null) {
            // User hasn't ordered a card
            print "<form id=\"user_" . $this->id . "_cardno_form\" action=\"javascript: grantCardNumber('" . $this->id . "')\" " . "method=\"post\">";
            print "<input type=\"hidden\" name=\"action\" value=\"grant-cardno\" />\n";
            print "<input type=\"submit\" value=\"bestill kort\" />\n";
            print "</form>";
        } elseif (!$this->getCardProduced()) {
            // User's card has been produced, but has not been delivered yet
            print "<form id=\"user_" . $this->id . "_cardproduced_form\" " . "action=\"javascript: setCardProduced('" . $this->id . "')\" " . "method=\"post\">";
            print "<input type=\"hidden\" name=\"action\" value=\"update-user-cardproduced\" />\n";
            print "<input type=\"submit\" value=\"kort produsert\" />\n";
            print "</form>";
        } elseif (!$this->getCardDelivered()) {
            // User's card has been produced, but has not been delivered yet
            print "<form id=\"user_" . $this->id . "_carddelivered_form\" " . "action=\"javascript: setCardDelivered('" . $this->id . "')\" " . "method=\"post\">";
            print "<input type=\"hidden\" name=\"action\" value=\"update-user-carddelivered\" />\n";
            print "<input type=\"submit\" value=\"kort er levert\" />\n";
            print "</form>";

        } else {
            // User has a membercard
            //print "kort mistet?";
        }
        print "  </td>\n";

        print "  <td id=\"user_" . $this->id . "_expires\">";
        if ($this->expires == '0000-00-00') {
            print "0";
        } else {
            if ($this->expires == NULL) {
                print "livsvarig";
            } else {
                print $this->expires;
            }
        }
        print "  </td>\n";

        print "  <td>";
        print "<div>";
        if ($this->cardno != NULL) {
            print "<form action=\"javascript: updateUserExpiry('" . $this->id . "')\" method=\"get\">";
            print "<input type=\"hidden\" name=\"action\" value=\"update-user-expiry\" />\n";
            print "<select name=\"newExpiryDate_" . $this->id . "\" id=\"newExpiryDate_" . $this->id . "\">\n";
            print "<option value=\"0000-00-00\">" . "ugyldig utløpsår" . "</option>\n";
            $loop = array('now'=>'inneværende år','+1 year'=>'neste år','+3 year'=>'tre år','+5 year'=>'fem år');
            
            foreach($loop as $time=>$label)
            	echo '<option ' . (getExpiryDate($time) == $this->expires?' selected="selected" ':'') . ' value="' . getExpiryDate($time) . '">' . $label . ' (' . getExpiryDate($time) . ')</option>';
            
            print "<option "  . ($this->expires === null?' selected="selected" ':'') .  " value=\"lifetime\">" . "livsvarig" . "</option>\n";
            print "<option value=\"2011-12-31\" >" . "2011, gammelt medlemskap" . "</option>\n";
            print "</select>\n";
            print "<input type=\"submit\" value=\"endre\" />\n";
            print "</form>";
        }
        print "</div>";
        print "</td>\n";

        print "<td id=\"user_" . $this->id . "_laststicker\">";
        print $this->getLastSticker();
        print "</td>\n";

        print "  <td>";
        if ($this->cardno != NULL) {
            print "<form action=\"javascript: updateLastSticker('" . $this->id . "')\" method=\"post\">";
            print "<div>";
            print "<input type=\"hidden\" name=\"action\" value=\"update-user-last-sticker\" />";
            print "<select name=\"newStickerDate_" . $this->id . "\" id=\"newStickerDate_" . $this->id . "\">";
            print "<option value=\"2011\">" . "I år, gammelt medlemskap" . "</option>\n";
            
            print "<option selected=\"selected\" value=\"" . getStickerPeriod("now") . "\">" . "inneværende år (" . getStickerPeriod("now") . ")" . "</option>\n";
            print "<option value=\"" . getStickerPeriod("+1 year") . "\">" . "neste år (" . getStickerPeriod("+1 year") . ")" . "</option>\n";
            print "<option value=\"" . getStickerPeriod("+3 year") . "\">" . "tre år (" . getStickerPeriod("+3 year") . ")" . "</option>\n";
            print "<option value=\"" . getStickerPeriod("+5 year") . "\">" . "fem år (" . getStickerPeriod("+5 year") . ")" . "</option>\n";
            print "<option value=\"0000-00-00\">ingen verdi</option>";
            print "</select>";
            print "<input type=\"submit\" value=\"endre\" />";
            print "</div>";
            print "</form>";
        }
        print "</td>\n";

        print "  <td>" . $this->getRegistered() . "</td>\n";
        print "  <td>" . $this->getLastUpdate(); "</td>\n";
        print "</tr>\n";
    }

    public function _registerUpdate($message) {
        $sql = "INSERT INTO din_userupdate VALUES (" .
            "NULL, " .
            "NOW( ), " .
            $this->id . ", " .
            $this->conn->quoteSmart($message) . ", " .
            ((getCurrentUser() == 0) ? $this->id : getCurrentUser()) .
            ")";
        $result = $this->conn->query($sql);
        if (DB :: isError($result) == true) {
            $this->conn->rollback();
            error("Database error (User::_registerUpdate()): " . $result->toString());
            return false;
        } else {
            $this->conn->commit();
        }
    }

    public function displayUpdates() {
        $sql = "SELECT d.*, CONCAT(d1.firstname, ' ', d1.lastname) AS updater " . "FROM din_userupdate d, din_user d1 " . "WHERE user_id_updated = $this->id " . "AND d.user_id_updated_by=d1.id ORDER BY date DESC";

        $result = & $this->conn->query($sql);
        if (DB :: isError($result) != true) {
            print "<table>";
            print "<tr><th colspan=\"3\">Liste over endringer</th></tr>";

            while ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
                print "<tr><td>" . formatDatetimeYearShort($row->date) . "</td><td>$row->comment</td><td> $row->updater</td></tr>";
            }
            print "</table>";
        } else {
            error("Users: " . $result->toString());
        }
    }

    public function resetPassword() {
        $newPassword = generatePassword();
        $sql = "UPDATE din_user SET " . "  password = PASSWORD('$newPassword'), " . "  passwordReset = 1 " . "WHERE" . "  id = $userid";
        $result = $conn->query($sql);
        if (DB :: isError($result) == true) {
            error("ResetPassword: " . $result->toString());
            notify("Tjenesten er midlertidig utilgjengelig, vennligst forsøk igjen senere.");
            return false;
        } else {
            $this->_registerUpdate('Brukernavn og nytt passord sendt til bruker.');
            return true;
        }
    }

    public function _sendPassword() {
        $sendto = $this->email;
        $subject = "Brukernavn og passord for Studentersamfundet Inside";
        $message = "Hei, $this->firstname $this->lastname!\n " . "\n" . "\nHer er din innloggingsinformasjon til Studentersamfundets medlemsider. " . "\n" . "\nDitt brukernavn er: $row->username" . "\nDitt passord er: $newPassword" . "\n" . "\nNår du logger på vil du bli bedt om å endre passord til noe som er lettere å huske." . "\n" . "\nhttps://inside.studentersamfundet.no/" . "\n" . "\nmvh" . "\n\nDet Norske Studentersamfund.";
        $headers = 'From: Det Norske Studentersamfund <medlemskap@studentersamfundet.no>' . "\r\n";
        if (mail($sendto, $subject, $message, $headers)) {
            notify("Nytt brukernavn og passord er sendt til din registrerte epostadresse. Bruk skjemaet under for å logge deg inn.");
        } else {
            notify("Det oppstod en feil under sending av epost. Vennligst kontakt" .
		        "<a href=\"mailto:support@studentersamfundet.no\">webansvarlig</a>.");
        }
    }

    // Send mail with notification of card delivery
    public function sendCardOrderedNotifyMail() {
        $sendto = $this->email;

        $subject = "Medlemskapet ditt er aktivert";
        $message = "Hei, " . $this->firstname . " " . $this->lastname . "!" .
        "\n\n" .
        "Vi har registrert at du har aktivert medlemskapet ditt i Det Norske Studentersamfund. " .
        "Du vil motta en e-post når medlemskortet ditt er produsert og klart til å hentes i Glassbaren på studentersamfundet. \n" .
        "\n" .
        "Som medlem vil du få informasjon om  arrangementer i vårt nyhetsbrev og rabatter på alt fra arrangementer" . 
        " til mat og drikke. I tillegg kan du være med og bestemme siden alle medlemmer har stemmerett ved Generalforsamlinger\n" . 
        " og Medlemsmøter i DNS.\n" . 
        "\n";
        
        //For mer informasjon om hva som skjer på Det Norske Studentersamfund, gå inn på vår nettside: http://www.studentersamfundet.no/ .\n" .
        
        if(time() < strtotime("2011-09-12")) //trenger ikke vise denne etter fadderhelga
        {
        	$message .= "Ny i DNS? Ikke meldt deg på fadderhelgen? 9-11. september kan alle nye medlemmer og aktive få se våre innerste ganger,".
        				" bli kjent med nye og gamle neufere og ikke minst få med seg en av våre legendariske internfester! Send mail til".
        				" nyidns@studentersamfundet.no eller finn ut mer på http://studentersamfundet.no/nyidns". "\n\n".

						"Virker noe av det vi holder på med på huset spennende?  Du kan bli med som aktiv i en eller mange av våre foreninger. sjekk http://studentersamfundet/bliaktiv\n\n".
						"Lik Det Norske Studentersamfund på facebook! http://www.facebook.com/studentersamfundet\n\n";
		}
		
        $message .= "Er det noe du lurer på kan du bare svare på denne eposten, så svarer vi så fort vi klarer.\n".
					"\n\n".
					"Virker noe av det vi holder på med på huset spennende?  Du kan bli med som aktiv i en eller mange av våre foreninger.\n Sjekk http://studentersamfundet/bliaktiv" .
					"\n\n" .
					"Mvh\n\n" .
					"Medlemskapsordningen\n" .
					"medlemskap@studentersamfundet.no\n" .
					"Det Norske Studentersamfund\n\n";

        $headers = 'From: Det Norske Studentersamfund <medlemskap@studentersamfundet.no>' . "\r\n";

        if (!mail($sendto, $subject, $message, $headers)) {
            notify("Det oppstod en feil under sending av epost. Vennligst kontakt" . "<a href=\"mailto:medlemskap@studentersamfundet.no\">webansvarlig</a>.");
        }
    }

    // Send mail with notification of card delivery
    public function sendCardProducedNotifyMail() {
        $sendto = $this->email;

        $subject = "Medlemskortet ditt er produsert";
        $message = "Hei, " . $this->firstname . " " . $this->lastname . "!" .
        "\n\n" .
        "Vi har nå produsert medlemskortet ditt i Det Norske Studentersamfund. " .
        "Det ligger nå klart til avhenting i Glassbaren på Studentersamfundet og kan hentes der i Glassbarens åpningstid. \n" .
        "\n" .
        "For mer informasjon om hva som skjer på Det Norske Studentersamfund, gå inn på vår nettside: http://www.studentersamfundet.no/ .\n" .
        "\n" .
        "Er det noe du lurer på kan du bare svare på denne eposten, så svarer vi så fort vi klarer.\n".
        "\n\n" .
        "Bli aktiv i dag!\n" .
        "www.studentersamfundet.no/bliaktiv\n" .
        "\n\n" .
        "Mvh\n\n" .
        "Medlemskapsordningen\n" .
        "medlemskap@studentersamfundet.no\n" .
        "Det Norske Studentersamfund\n\n";

        $headers = 'From: Det Norske Studentersamfund <medlemskap@studentersamfundet.no>' . "\r\n";

        if (!mail($sendto, $subject, $message, $headers)) {
            notify("Det oppstod en feil under sending av epost. Vennligst kontakt" . "<a href=\"mailto:medlemskap@studentersamfundet.no\">webansvarlig</a>.");
        }
    }

    // Send mail with notification of card delivery
    public function sendRenewedMembershipRegisteredNotifyMail () {
        $sendto = $this->email;

        $subject = "Medlemsskapet ditt i Det Norske Studentersamfund er registrert som fornyet";
        $message = "Hei, " . $this->firstname . " " . $this->lastname . "!" .
        "\n\n" .
        "Vi har registrert at du har fornyet medlemsskapet ditt i Det Norske Studentersamfund. " .
        "Medlemsoblat som viser at du har medlemskap kan hentes i Glassbaren på Studentersamfundet i Glassbarens åpningstid. Ta med medlemskortet ditt. \n" .
        "Hvis du har mistet medlemskortet ditt kan du få ordnet nytt kort i Glassbaren også, eller bestille det fra nettbutikken.\n".
        "\n" .
        "For mer informasjon om hva som skjer på Det Norske Studentersamfund, gå inn på vår nettside: http://www.studentersamfundet.no/ .\n" .
        "\n" .
        "Er det noe du lurer på kan du bare svare på denne eposten, så svarer vi så fort vi klarer.\n".
        "\n\n" .
        "Bli aktiv i dag!\n" .
        "www.studentersamfundet.no/bliaktiv\n" .
        "\n\n" .
        "Mvh\n\n" .
        "Medlemskapsordningen\n" .
        "medlemskap@studentersamfundet.no\n" .
        "Det Norske Studentersamfund\n\n";

        $headers = 'From: Det Norske Studentersamfund <medlemskap@studentersamfundet.no>' . "\r\n";

        if (!mail($sendto, $subject, $message, $headers)) {
            notify("Det oppstod en feil under sending av epost. Vennligst kontakt" . "<a href=\"mailto:medlemskap@studentersamfundet.no\">webansvarlig</a>.");
        }
    }

    public function sendNewAddressMail ($newstreet, $newzipcode) {
        $sendto = $this->email;

        $subject = "Du er registrert med feil postadresse";
        $message = "Hei, " . $this->firstname . " " . $this->lastname . "!" .
        "\n\n" .
        "Vi har sendt ut en postsending til deg, og fått beskjed fra posten " .
        "om at du har flyttet fra \"" .
        $this->street . ", " . $this->zipcode . " " . $this->postarea .
        "\" til \"" .
        $newstreet . ", " . $newzipcode . " " . $this->getPostarea($newzipcode) .
        "\".\n" .
        "Brevet er videresendt til denne nye adressen. " .
        "Hvis du ikke har mottatt dette brevet, eller ikke har flyttet kan " .
        "du logge inn på våre medlemssider og oppdatere din adresse. " .
        "Logg inn på denne siden: " .
        "https://www.studentersamfundet.no/inside - ditt brukernavn er " .
        "\"" . $this->username . "\".\n" .
        "\n" .
        "Hvis du ikke har mottatt medlemskortet vil vi at du svarer på denne " .
        "e-posten etter du har oppdatert din adresse og ber om å få tilsendt " .
        "nytt medlemskort.\n" .
        "\n\n\n" .
        "Mvh\n\n" .
        "Medlemskapsordningen\n" .
        "medlemskap@studentersamfundet.no\n" .
        "Det Norske Studentersamfund\n\n";

        $headers = 'From: Det Norske Studentersamfund <medlemskap@studentersamfundet.no>' . "\r\n";

        if (mail($sendto, $subject, $message, $headers)) {
            notify("Melding om ny adresse er sendt til $this->email");
        } else {
            notify("Det oppstod en feil under sending av epost. Vennligst kontakt" . "<a href=\"mailto:medlemskap@studentersamfundet.no\">webansvarlig</a>.");
        }
    }

    public function _sendWrongAddressMail($link = null) {
        $sendto = $this->email;
        $linkMessage = "";
        if ($link) {
            $linkMessage = "Brevet vi har forsøkt å sende kan du også " .
                "lese på nett fra denne adressen:\n$link\n\n";
        }
        $subject = "Postforsendelse er ikke kommet frem til addressen din";
        $message = "Hei, " . $this->firstname . " " . $this->lastname . "!" .
        "\n\n" .
        "Vi har sendt ut en postsending til deg, og fått beskjed fra Posten ".
        "om at du var ukjent på din oppgitte adresse. \n" .
        "Vi sendte brevet til følgende adresse: \"" . $this->street . ", " .
        $this->zipcode . " " . $this->postarea . "\". " .
        "På grunn av tilbakemeldingen om at du er ukjent på adressen ".
        "er brevet så blitt makulert av Posten, på oppdrag av oss." .
        "\n" .
        $linkMessage .
        "\n" .
        "Hvis du ikke har mottat medlemskortet eller oblatet ditt er det " .
        "mest sannsynlig det som var i denne postforsendelsen. Hvis du fortsatt " .
        "bor på adressen over, eller har meldt flytting fra denne adressen må " .
        "du klage til Posten, enten på telefon 810 00 710, eller gjennom skjemaet " .
        "du finner på denne siden: http://www.posten.no/Kundeservice .\n" .
        "Be om svar på epost og videresend denne til medlemskap@studentersamfundet.no " .
        "så sender vi deg et nytt medlemskort kostnadsfritt. \n" .
        "\n".
        "Hvis du derimot har oppgitt feil adresse, eller har flyttet uten å " .
        "melde flytting må du logge inn på våre medlemsider, oppdatere adressen " .
        "din og bestille nytt kort i nettbutikken. Da vil du automatisk få tilsendt " .
        "nytt kort. Vi må ta 35 kroner for å dekke våre kostnader. " .
        "Våre medlemsider finner du på https://inside.studentersamfundet.no/ ." .
        "Ditt brukernavn er \"" . $this->username . "\". \n".
        "\n\n\n" .
        "Mvh\n\n" .
        "Medlemskapsordningen\n" .
        "medlemskap@studentersamfundet.no\n" .
        "Det Norske Studentersamfund\n\n";

        $headers = 'From: Det Norske Studentersamfund <medlemskap@studentersamfundet.no>' . "\r\n";

        if (mail($sendto, $subject, $message, $headers)) {
            notify("Melding om ugyldig adresse er sendt til $this->email");
        } else {
            notify("Det oppstod en feil under sending av epost. Vennligst kontakt" . "<a href=\"mailto:medlemskap@studentersamfundet.no\">webansvarlig</a>.");
        }
    }

    /*
     * Print the status of the user's membership
     * return: string
     */
    public function membershipStatus() {
        if ($this->expires == "0000-00-00") {
	    $panel_class = 'danger';
	    $panel_text  = "Du har ikke registrert medlemskap. <a href='https://inside.studentersamfundet.no/index.php?page=register-membership'>Kjøp eller registrer medlemskap</a>.";
	} elseif ($this->expires == "") {
	    $panel_class = 'success';
            $panel_text = "Du har livsvarig medlemskap.";
	} elseif (strtotime($this->expires) < strtotime("now")) {
	    $panel_class = 'danger';
            $panel_text = "Du har ikke registrert gyldig medlemskap i år (medlemskapet ditt gikk ut " . date("d.m.Y", strtotime($this->expires)) . "). <a href='https://inside.studentersamfundet.no/index.php?page=register-membership'>Kjøp eller registrer medlemskap</a>.";
	} elseif (strtotime($this->expires) > strtotime("now")) {
            if ($this->getCardDelivered()) {
                if ($this->lastSticker < date("Y", strtotime($this->expires))) {
		    $panel_class = 'warning';
                    $panel_text = "Du har aktivert medlemskapet ditt, og du kan hente oblat til å klistre på medlemskortet ditt i Glassbaren på Det Norske Studentersamfund.";
                } else {
		    $panel_class = 'success';
                    $panel_text = "Du har gyldig medlemskap (gyldig til " . date("d. m. Y", strtotime($this->expires)) . ").";
                }
	    } elseif ($this->getCardProduced()) {
		$panel_class = 'warning';
                $panel_text = "Medlemskortet ditt ligger klar til henting i Glassbaren på Det Norske Studentersamfund.";
            } else {
		$panel_class = 'success';
                $panel_text = "Medlemskapet ditt er registrert, og medlemskortet ditt for " . date("Y", strtotime($this->expires)) . " produseres. Du vil få en e-post av oss når det er klart til å hentes.";
            }
        } else {
	    $panel_class = 'warning';
	    $panel_text = "Ukjent medlemskapsstatus";
	}
	
	$html = "<div class='alert alert-$panel_class'>$panel_text</div>";

	return $html;
    }

  function setMembershipCard($membershipCard) {
    if (!is_null($this->membershipCard)) {
      return false;
    }
    
    $membershipCard->setUserId($this->id);
    $this->membershipCard = $membershipCard;
  }

  function getMembershipCard() {
    if (is_null($this->membershipCard)) {
      $this->membershipCard = new MembershipCard();
      if (!$this->membershipCard->findByUserId($this->id)) {
        $this->membershipCard = null;
      }
    }
    return $this->membershipCard;
  }
}
?>
