<?php
/* 
 * Lookup a members phone number and return a member-like object.
 *
 * Request:
 *
 * $ curl /snapporder/api/query.php?phone=%2B4748105885 # %2B is a +
 *
 * Response:
 *
 * {
 *   "phone": "+4742345678",
 *   "membership_status": 1,  // 0: registrert, 1: medlem, 2: aktivt medlem
 *   "expires": "2015-04-23",
 *   "memberid": "4331",
 *   "cardno": "12345",
 *   "firstname": "Jon",
 *   "lastname": "Hansen",
 *   "email": "jon@uio.no",
 *   "registration_status": "partial" // "partial" means show link
 *   "registration_url": "/snapporder/register_partial.php?userid=4331&token=lol"
 * }
 *
 */
set_include_path("../../includes/");

require_once("../../inside/credentials.php");
require_once("../../inside/functions.php");
require_once("../../includes/DB.php");

require_once("../lib/CryptoHelper.php");
require_once("../lib/functions.php");
require_once("../config.php");

$crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);

/* Checks */
if(!isset($_GET['phone'])) {
    set_response_code(400);
    echo json_encode(array('error' => 'Missing param phone'));
    die();
}
$phone = $_GET['phone'];
$phone = clean_phonenumber($phone);
if( !valid_phonenumber($phone) ) {
    set_response_code(400);
    echo json_encode(array('error' => 'Not a phone number', 'query' => $_GET));
    die();
}

/* Connect to database */
$options = array( 'debug' => 2, 'portability' => DB_PORTABILITY_ALL );
$conn = DB::connect(getDSN(), $options);
if(DB :: isError($conn)) {
    echo $conn->toString();
    set_response_code(500);
    echo json_encode(array('error' => 'Could not connect to DB'));
    die();
}
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

/* Search in phone number table for phone number */
$user_id = getUseridFromPhone($phone);
if( $user_id === false ) {
    echo json_encode( array('result' => 'No matching users'));
    die();
}

/* Get and format user */
$user = get_user($user_id);

/* Add back phone number from query */
$user['phone'] = $phone;

/* Add register url if needed */
if($user['registration_status'] === "partial") {
    $user['registration_url'] = generate_registration_url($user, SECRET_KEY);
}

/* Return encrypted user object */
echo json_encode($user);
//echo $crypt->encrypt(json_encode($user));

?>
