<?php

class ValidationException extends Exception {}

class InsideDatabaseException extends Exception {}

/* Username based on firstname and lastname, max 12 chars */
function generate_username($data) {
    $firstname = preg_replace('/[^\w]/', '', $data['firstname']);
    $firstname = substr(strtolower($firstname), 0, 5);

    $lastname = preg_replace('/[^\w]/', '', $data['lastname']);
    $lastname = substr(strtolower($lastname), 0, 2);
    $rand = substr(uniqid("", true), -5);

    return $firstname.$lastname.$rand;
}

function add_user($data) {
    global $conn;

    /* User table  */
    $phone = $data['phone']; // used later
    unset($data['phone']);

    /* Add our own initial values */
    $data['username'] = generate_username($data);
    $data['source'] = "snapporder";
    $data['registration_status'] = "partial";

    /* Membership expiry */
    /* One year from today (default) */
    if( !isset($data['purchased']) ) {
        $data['purchased'] = date_create();
    }
    /* ...or one year from specified date */
    $data['expires'] = date_format(date_modify($data['purchased'], "+1 year"), "Y-m-d");
    unset($data['purchased']); // dont save purchase date

    $cols = array_keys($data);
    $values = array_values($data);

    $sth = $conn->autoPrepare("din_user", $cols, DB_AUTOQUERY_INSERT);
    $res = $conn->execute($sth, $values);

    if( DB::isError($res) ) {
        throw new InsideDatabaseException($res->toString);
    }

    $user_id = get_user_id_by_username($data['username']);

    /* Phonenumber table */
    $cols = array('user_id', 'number', 'validated');
    $values = array($user_id, $phone, 1);

    $sth = $conn->autoPrepare("din_userphonenumber", $cols, DB_AUTOQUERY_INSERT);
    $res = $conn->execute($sth, $values);

    if( DB::isError($res) ) {
        throw new InsideDatabaseException($res->toString);
    }

    log_userupdate($user_id, "User registered."); // for legacy
    log_userupdate($user_id, "Medlemskap registrert via SnappOrder.");

    return $user_id;
}

/* Get user object with group ids and membership status */
function get_user($user_id) {
    global $conn;

    /* Note: Field 'expires' can have the following meanings: 
     *  - 0000-00-00 (default): No membership (never has)
     *  - a date < NOW(): Expired membership
     *  - a date >= NOW(): Valid membership
     *  - NULL: Lifelong membership
     */
    $cols = array('id', 'firstname', 'lastname', 'email', 'expires', 'cardno', 'registration_status', 'birthdate');
    $sql_group_ids = "GROUP_CONCAT(group_id) AS group_ids";
    $sql_is_member = "expires > NOW() OR expires IS NULL AS is_member";
    $sql = "SELECT ".implode($cols, ",").",$sql_group_ids,$sql_is_member FROM din_user AS users LEFT JOIN din_usergrouprelationship AS ug ON users.id=ug.user_id WHERE users.id=$user_id GROUP BY users.id";
    $res = $conn->query($sql);
    if( DB::isError($res) ) {
        throw new InsideDatabaseException($res->toString);
    }
    if($res->numRows() === 0) {
        return false;
    }
    $res->fetchInto($user);

    /* Membership status according to spec.
     * Status codes:
     * 0 - Registered
     * 1 - Member
     * 2 - Active member
     */
    $groups = explode(",", $user['group_ids']);
    $user['membership_status'] = 0;

    if($user['is_member'] !== "0") {
        // Group id mappings 1:dns-alle, 2: dns-aktiv, 3:administrator, 4+:orgunits/special groups
        if(in_array("2", $groups)) {
            $user['membership_status'] = 2;
        } else {
            $user['membership_status'] = 1;
        }
    }
    /* Clean up user object */
    unset($user['is_member']);
    unset($user['group_ids']);
    $user['memberid'] = $user['id']; // rename
    unset($user['id']);
    if($user['birthdate'] == "0000-00-00") {
        unset($user['birthdate']);
    }

    return $user;
}
function get_user_id_by_username($username) {
    global $conn;

    $sql = "SELECT id FROM din_user WHERE username='$username'";
    $res = $conn->query($sql);
    if( DB::isError($res) ) {
        throw new InsideDatabaseException($res->toString);
    }
    if( $res->numRows() === 0 ) {
        return false;
    }
    $res->fetchInto($data);
    return $data['id'];
}
/* Compare two hashes */
function hash_compare($a, $b) {
    if (!is_string($a) || !is_string($b)) {
        return false;
    }

    $len = strlen($a);
    if ($len !== strlen($b)) {
        return false;
    }

    $status = 0;
    for ($i = 0; $i < $len; $i++) {
        $status |= ord($a[$i]) ^ ord($b[$i]);
    }
    return $status === 0;
}
function create_token($user, $secret_key, $timestamp=NULL) {
    // Based on this: https://github.com/django/django/blob/master/django/contrib/auth/tokens.py#L50
    if($timestamp === NULL) {
        $timestamp = date_format(date_create(), "Y-m-d");
    }
    $message = $user['memberid'].$user['registration_status'].$timestamp;
    $user_hash = hash_hmac("sha256", $message, $secret_key);

    return $timestamp. "," .$user_hash;
}

function check_token($user, $token, $secret_key) {
    /* Check that a password reset token is correct for a given user. */

    // Parse the token
    list($ts, $hash) = explode(",", $token);

    // Check that the timestamp/uid has not been tampered with
    if( !hash_compare(create_token($user, $secret_key, $ts), $token) ) {
        return false;
    }

    // Check the timestamp is within limit
    $n_days_ago = date_modify(date_create(), "-".REGISTRATION_URL_TIMEOUT_DAYS." days");
    if( $n_days_ago > clean_date($ts) ) {
        return false;
    }

    return true;
}
function generate_registration_url($user, $secret_key) {
    $token = create_token($user, $secret_key);
    $server_name = isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ? $_SERVER['HTTP_X_FORWARDED_SERVER'] : $_SERVER['SERVER_NAME'];
    $scheme ="http".((empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off')?'':'s');
    $url = "$scheme://".$server_name."/snapporder/activate.php?userid=".$user['memberid']."&token=$token";

    return $url;
}
function date_picker($id, $output=true) {
    $html = "";
    // day
    $html .= '<select id="' .$id. '_day" name="day">';
    $html .= '<option value="">Dag</option>';
    for($i=1; $i<=31; $i++) {
        $selected = (isset($_POST['day']) && $_POST['day'] == $i) ? " selected" : "";
        $html .= "<option value=\"$i\"$selected>$i</option>";
    }
    $html .= "</select>";

    // month
    $html .= '<select id="' .$id. '_month" name="month">';
    $html .= '<option value="">Måned</option>';
    $months = array("januar", "februar", "mars", "april", "mai", "juni", "juli", "august", "september",
        "oktober", "november", "desember");
    $i = 1;
    foreach($months as $month) {
        $selected = (isset($_POST['month']) && $_POST['month'] === "$i") ? " selected" : "";
        $html .= '<option value="' .$i. '"' .$selected. '>' .ucfirst($month). '</option>';
        $i++;
    }
    $html .= "</select>";

    // year
    $html .= '<select id="' .$id. '_year" name="year">';
    $html .= '<option value="">År</option>';
    for($i=date("Y"); $i >= date("Y")-100; $i--) {
        $selected = (isset($_POST['year']) && $_POST['year'] == $i) ? " selected" : "";
        $html .= "<option value=\"$i\"$selected>$i</option>";
    }
    $html .= "</select>";

    if($output) {
        echo $html;
    }
    return $html;
}
function institutions($output=true) {
    global $conn;

    $sql = "SELECT * FROM studiesteder";
    $res = $conn->getAll($sql);
    if( DB::isError($res) ) {
        throw new InsideDatabaseException($res->toString);
    }
    $institutions = $res;

    /* Format */
    $html = "";
    $html .= '<select id="id_place_of_study" name="place_of_study">';
    foreach($institutions as $place) {
        // selection
        $selected = "";
        if( isset($_POST['place_of_study']) && $_POST['place_of_study'] == $place['id'] ) {
            $selected = " selected";
        } else if( $place['id'] == 22 && !isset($_POST['place_of_study']) ) {
            $selected =" selected" ;
        }

        $name = iconv("ISO-8859-1", "UTF-8", $place['navn']); // DB is latin1
        $html .= '<option value="' .$place['id']. '"' .$selected. '>' .$name. '</option>';
    }
    $html .= "</select>";

    if($output) {
        echo $html;
    }

    return $html;
}
function mailchimp_subscribe($data, $list_id, $api_key) {
    // Ref: http://apidocs.mailchimp.com/api/2.0/lists/subscribe.php
    if(strlen($api_key) == 0) {
        return false;
    }

    $double_optin = false;
    $send_welcome = false;
    list($not_used, $dc) = explode("-", $api_key);
    $submit_url = "https://$dc.api.mailchimp.com/2.0/lists/subscribe.json";

    $data = array(
        'apikey' => $api_key,
        'id' => $list_id,
        'email' => array('email' => $data['email']),
        'merge_vars' => array('fname' => $data['firstname'], 'lname' => $data['lastname']),
        'double_optin' => $double_optin,
        'send_welcome' => $send_welcome

    );
    $payload = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $submit_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $result = curl_exec($ch);
    $data = (array) json_decode($result);
    if( isset($data['status']) && $data['status'] === "error") {
        $message = "Request: '" .var_export(curl_getinfo($ch), true). "' \nPayload: '" .var_export($payload, true). "'";
        @mail("kak-edb@studentersamfundet.no", "[Inside] Could not add a user to a mailchimp list.", $message);
        curl_close ($ch);
        return false;
    } 

    curl_close ($ch);
    return true;
}

function update_user($data) {
    global $conn;

    $birthdate_sql = "";
    if( strlen($data['birthdate']) > 0) {
        $birthdate_sql = "birthdate=" .$conn->quoteSmart($data['birthdate']).",";
    }

    // user: update existing user, activation_status="full", form data
    $sql = "UPDATE din_user SET ";
    $sql .= "username=" .$conn->quoteSmart($data['username']).",";
    $sql .= "password=PASSWORD(" .$conn->quoteSmart($data['password'])."),";
    $sql .= $birthdate_sql;
    $sql .= "placeOfStudy=" .$conn->quoteSmart($data['place_of_study']).",";
    $sql .= "registration_status='full'";
    $sql .= " WHERE id = " . $conn->quoteSmart($data['userid']);
    $res = $conn->query($sql);
    
    if (DB::isError($res)) {
        throw new InsideDatabaseException($res->toString);
    }
    return true;
}
function update_user_groups($data) {
    global $conn;

    // add to dns-alle (2)
    $group = array(
        'user_id' => $data['userid'],
        'group_id' => 2
    );
    $res = $conn->autoExecute('din_usergrouprelationship', $group, DB_AUTOQUERY_INSERT);

    if (DB::isError($res)) {
        // allready exists is fine
    }
    return true;
}

function valid_date($date) {
    // try parsing
    if(!clean_date($date)) {
        return false;
    }
    return $date;
}
function validate_birthdate($data, $optional=true) {
    $keys = array('year', 'month','day');
    $values = array();

    foreach( $keys as $key) {
        // If started filling out date of birth
        if(strlen($data[$key]) !== 0) {
            $optional = false;
        }
        // year
        $values[] = $data[$key];
    }

    if($optional) {
        return ""; // skip
    }

    // try parsing
    $date = implode('-', $values);
    if(!clean_date($date)) {
        return false;
    }
    return $date;
}

function validate_activation_form($data) {

    // username: length, ascii, exists
    $data['username'] = trim($data['username']);
    if( !validate_username_length($data['username']) ) {
        throw new ValidationException("Brukernavet må være mellom 3 og 12 tegn");
    }
    if( !validate_username_chars($data['username']) ) {
        throw new ValidationException("Brukernavnet kan kun inneholde små bokstaver.");
    }
    if( get_user_id_by_username($data['username'], false) !== false) {
        throw new ValidationException("Brukernavnet er allerede i bruk.");
    }

    // password: entropy, quotes
    $data['password'] = trim($data['password']);
    if( !validate_password_length($data['password']) ) {
        throw new ValidationException("Passordet må være på minst 8 tegn.");
    }
    if( !validate_password_chars($data['password']) ) {
        throw new ValidationException("Passordet kan ikke inneholde enkel- eller dobbelfnutt eller bakslask.");
    }

    // birthdate (optional)
    $valid_date = validate_birthdate($data);
    if( $valid_date === false ) {
        throw new ValidationException("Ugyldig fødselsdato");
    }
    $data['birthdate'] = $valid_date; // Note: could be: ""

    // place of study (optional)
    if( !is_numeric($data['place_of_study']) ) {
        throw new ValidationException("Ugyldig studiested");
    }

    return $data;
}
function save_activation_form($data) {
    update_user($data);
    update_user_groups($data);

    log_userupdate($data['userid'], "Membership activated.");
    
    // weekly newsletter (mailchimp)
    if(isset($data['newsletter']) && $data['newsletter'] === "1" && MAILCHIMP_API_KEY !== "") {
        if( mailchimp_subscribe($data, MAILCHIMP_LIST_ID, MAILCHIMP_API_KEY) ) {
            log_userupdate($data['userid'], "Lagt til nyhetsbrevet.");
        }
    }

    return true;
}
?>

