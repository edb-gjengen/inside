<?php
/* Set HTTP response code */
function set_response_code($code) {
    if(!is_int($code)) {
        return false;
    }
    header('X-Ignore-This: something', true, $code);
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
function valid_phonenumber($phone) {
    return is_numeric($phone) && strlen($phone) === 8;
}

?>
