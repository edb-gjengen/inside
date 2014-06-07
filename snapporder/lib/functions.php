<?php

/* Set HTTP response code */
function set_response_code($code) {
    if(!is_int($code)) {
        return false;
    }
    header('X-Ignore-This: something', true, $code);
}

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
        set_response_code(500);
        echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
        die();
    }

    $user_id = get_user_id_by_username($data['username']);

    /* Phonenumber table */
    $cols = array('user_id', 'number', 'validated');
    $values = array($user_id, $phone, 1);

    $sth = $conn->autoPrepare("din_userphonenumber", $cols, DB_AUTOQUERY_INSERT);
    $res = $conn->execute($sth, $values);

    if( DB::isError($res) ) {
        set_response_code(500);
        echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
        die();
    }

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
    $cols = array('id', 'firstname', 'lastname', 'email', 'expires', 'cardno', 'registration_status');
    $sql_group_ids = "GROUP_CONCAT(group_id) AS group_ids";
    $sql_is_member = "expires > NOW() OR expires IS NULL AS is_member";
    $sql = "SELECT ".implode($cols, ",").",$sql_group_ids,$sql_is_member FROM din_user AS users LEFT JOIN din_usergrouprelationship AS ug ON users.id=ug.user_id WHERE users.id=$user_id GROUP BY users.id";
    $res = $conn->query($sql);
    if( DB::isError($res) ) {
        set_response_code(500);
        echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
        die();
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

    return $user;
}
function get_user_id_by_username($username) {
    global $conn;

    $sql = "SELECT id FROM din_user WHERE username='$username'";
    $res = $conn->query($sql);
    if( DB::isError($res) ) {
        set_response_code(500);
        echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
        die();
    }
    $res->fetchInto($data);
    return $data['id'];
}
function clean_phonenumber($pn) {
    $pn = preg_replace('/[^0-9\+]/', '', $pn); // remove everything except valid chars
    $pn = preg_replace('/^00/','+', $pn); // replace starting 00 with +
    // norwegian phone numbers
    if( strlen($pn) === 8 && ($pn[0] === "4" || $pn[0] === "9") ) {
        $pn = "+47".$pn;
    }
    return $pn;
}
// ISO-8601 Y-m-d
function clean_date($date) {
    // returns a DateTime object
    return date_create_from_format('Y-m-d', $date);
}
// E.164
function valid_phonenumber($phone) {
    return preg_match('/^\+?\d{8,15}$/i', $phone);
}
function valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
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
    $url = $_SERVER["REQUEST_SCHEME"]."://".$server_name."/snapporder/activate.php?userid=".$user['memberid']."&token=$token";

    return $url;
}
?>
