<?php
set_include_path("../../includes/");
require_once("../../inside/credentials.php");
require_once("../../inside/functions.php");
require_once("../../includes/DB.php");

require_once("../lib/CryptoHelper.php");
require_once("../config.php");
/* 
 * Lookup a members phone number and return a member-like object.
 *
 * Request:
 *
 * $ curl /snapporder/api/query.php?phone=48105885
 *
 * Response:
 *
 * {
 *   "phone":" 42345678",
 *   "membership_status": 1, // 0 - ikke medlem, 1 - medlem, 2 - aktiv
 *   "expires": "2015-04-23",
 *   "cardno": "12345",
 *   "firstName": "Jon",
 *   "lastName": "Hansen",
 *   "email": "jon@uio.no"
 * }
 *
 */
$crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);

/* Checks */
if(!isset($_GET['phone'])) {
    http_response_code(400);
    echo json_encode(array('error' => 'Missing param phone'));
    die();
}
$phone = mysql_real_escape_string($_GET['phone']);
if(!is_numeric($phone) || strlen($phone) !== 8 ) {
    http_response_code(400);
    echo json_encode(array('error' => 'Not a phone number'));
    die();
}

/* Connect to database */
$options = array( 'debug' => 2, 'portability' => DB_PORTABILITY_ALL );
$conn = DB::connect(getDSN(), $options);
if(DB :: isError($conn)) {
    echo $conn->toString();
    http_response_code(500);
    echo json_encode(array('error' => 'Could not connect to DB'));
    die();
}

/* Search in phone number table for phone number */
$conn->setFetchMode(DB_FETCHMODE_ASSOC);
$sql = "SELECT user_id FROM din_userphonenumber WHERE number='$phone'";
$res = $conn->query($sql);
if( DB::isError($res) ) {
    http_response_code(500);
    echo json_encode( array('error' => 'db_error', 'error_message' => $res->toString() ) );
    die();
}
// empty?
if( $res->numRows() == 0) {
    echo json_encode( array('result' => 'No matching users'));
    die();
}
$res->fetchInto($row);
$user_id = $row['user_id'];

/* Get user object with group ids and membership status */
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
/* Add back phone number from query */
$user['phone'] = $phone;

/* Clean up user object */
unset($user['is_member']);
unset($user['group_ids']);

/* Return encrypted user object */
echo json_encode($user);
//echo $crypt->encrypt(json_encode($user));

?>
