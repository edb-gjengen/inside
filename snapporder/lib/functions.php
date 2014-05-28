<?php
require_once("../../inside/functions.php");

/* Set HTTP response code */
function set_response_code($code) {
    if(!is_int($code)) {
        return false;
    }
    header('X-Ignore-This: something', true, $code);
}
function generate_username($data) {
    // TODO improve this, not unique enough
    $firstname = preg_replace('/[^\w]/', '', $data['firstname']);
    $firstname = substr(strtolower($firstname), 0, 6);

    $lastname = preg_replace('/[^\w]/', '', $data['lastname']);
    $lastname = substr(strtolower($lastname), 0, 3);
    return $firstname.$lastname.substr(uniqid(), -3);
}
function add_user($data) {
    global $conn;

    /* User table  */
    $phone = $data['phone'];
    unset($data['phone']);
    $data['username'] = generate_username($data);
    $cols = array_keys($data);
    $values = array_values($data);
    $sth = $conn->autoPrepare("din_user", $cols, DB_AUTOQUERY_INSERT);


    $res = $conn->execute($sth, $values);
    if( DB::isError($res) ) {
        echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
        die();
    }

    $user_id = get_user_id_by_username($data['username']);

    /* Phonenumber table */
    $sth = $conn->autoPrepare("din_userphonenumber", array('user_id', 'number'), DB_AUTOQUERY_INSERT);
    $res = $conn->execute($sth, array($user_id, $phone));

    if( DB::isError($res) ) {
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
    $cols = array('id', 'firstname', 'lastname', 'email', 'expires', 'cardno');
    $sql = "SELECT ".implode($cols, ",").",GROUP_CONCAT(group_id) AS group_ids,expires > NOW() OR expires IS NULL AS is_member FROM din_user AS u, din_usergrouprelationship AS ug WHERE u.id=$user_id AND u.id=ug.user_id GROUP BY user_id";
    $res = $conn->query($sql);
    if( DB::isError($res) ) {
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

    return $user;
}
function get_user_id_by_username($username) {
    global $conn;

    $sql = "SELECT id FROM din_user WHERE username='$username'";
    $res = $conn->query($sql);
    if( DB::isError($res) ) {
        echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
        die();
    }
    $res->fetchInto($data);
    var_dump($data);
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
// E.164
function valid_phonenumber($phone) {
    return preg_match('/^\+?\d{8,15}$/i', $phone);
}
function valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
