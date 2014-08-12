<?php 
/* 
 * Registers a user in the user table
 *
 * Request (new):
 *
 * $ curl /snapporder/api/register.php
 * {
 *   "phone": "+4742345678",
 *   "firstname": "Jon",
 *   "lastname": "Hansen",
 *   "email": "jon@uio.no",
 *   "purchased": "2004-02-12"    // optional, format: ISO-8601 date
 *   "source": "snapporder"   // optional, possible values: "snapporder", "sms" or "manual"
 *   "membership_trial": "buddy"  // optional, gives free membership in autumn, possible value: "buddy"
 * }
 *
 * Response:
 *
 * {
 *   "phone": "+4742345678",
 *   "membership_status": 1,  // 0: registrert, 1: medlem, 2: aktivt medlem
 *   "expires": "2015-04-23",
 *   "memberid": "4331",
 *   "firstname": "Jon",
 *   "lastname": "Hansen",
 *   "email": "jon@uio.no",
 *   "birthdate": "1985-03-01",
 *   "registration_status": "partial"  // "partial" means show link
 *   "membership_trial": "buddy"       // possible value: "buddy", user has a trial membership, unset if not
 *   "registration_url": "/snapporder/register_partial.php?userid=4331&token=lol"
 * }
 *
 * Request (renewal):
 *
 * $ curl /snapporder/api/register.php
 * {
 *   "phone": "+4742345678",
 *   "purchased": "2004-02-12"   // optional, format: ISO-8601 date
 *   "type": "renewal"           // optional, possible value: "renewal"
 *   "source": "snapporder"  // optional, possible values: "snapporder", "sms" or "manual"
 * }
 *
 */

/* Pull in Inside */
set_include_path("../../inside/");
require_once("functions.php");
require_once("User.php");
set_include_path("../../includes/");
require_once("DB.php");

/* Our own */
require_once("../lib/CryptoHelper.php");
require_once("../lib/functions.php");
require_once("../config.php");

/* Validate request */
$body = file_get_contents('php://input');
if(strlen($body) === 0) {
    set_response_code(400);
    echo json_encode(array('error' => 'Invalid request'));
    die();
}

/* Decrypt body */
$crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);
$body = $crypt->decrypt(trim($body));
$data = (array) json_decode($body);
if($data === NULL) {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Can\'t decode body'));
    die();
}
/* Check type of registration and set valid fields */
$reg_type = "new";
if( isset($data['type']) && $data['type'] === "renewal" ) {
    /* Renewal */
    $required_keys = array('phone');
    $reg_type = $data['type'];
} else {
    $required_keys = array('firstname', 'lastname', 'phone', 'email');
}

$valid_keys = $required_keys;
$valid_keys = array_merge($valid_keys, array('purchased', 'source', 'type', 'membership_trial'));

/* Validate supplied data */
foreach($required_keys as $key) {
    if(!array_key_exists($key, $data)) {
        set_response_code(400);
        echo $crypt->json_encode_and_encrypt(array('error' => 'missing required field \''.$key.'\''));
        die();
    }
}
foreach($data as $key => $value) {
    if(!in_array($key, $valid_keys)) {
        set_response_code(400);
        echo $crypt->json_encode_and_encrypt(array('error' => 'unknown field \''.$key.'\''));
        die();
    }
}

$data['phone'] = clean_phonenumber($data['phone']);
if( !valid_phonenumber($data['phone']) ) {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Not a phone number:'.$data['phone']));
    die();
}

if( $reg_type === "new" && !valid_email($data['email']) ) {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Not an email: '.$data['email']));
    die();
}

/* Connect to database */
$options = array( 'debug' => 2, 'portability' => DB_PORTABILITY_ALL );
$conn = DB::connect(getDSN(), $options);
if(DB :: isError($conn)) {
    echo $conn->toString();
    set_response_code(500);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Could not connect to DB'));
    die();
}
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

/* Existing user? */
if( $reg_type === "new" && getUseridFromEmail($data['email']) !== false) {
    set_response_code(409);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Existing user with email: '.$data['email']));
    die();
}
$user_id = getUseridFromPhone($data['phone']);
if( $reg_type === "new" && $user_id !== false ) {
    set_response_code(409);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Existing user with phone: '.$data['phone']));
    die();
}
/* Renewal of membership? */
if( $reg_type === "renewal" && $user_id === false ) {
    set_response_code(409);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Could not find user with phone: '.$data['phone']));
    die();
}
$data['user_id'] = $user_id;
if( $reg_type === "renewal") {
    $user = get_user($user_id);
    /* Don't allow renewal of an existing valid membership. */
    if( $user['membership_status'] !== 0 ) {
        set_response_code(409);
        echo $crypt->json_encode_and_encrypt(array('error' => 'Cannot renew, user with phone '.$data['phone'].' has a valid membership until: '.$user['expires']));
        die();
    }
}

/* validate firstname and lastname */
if( $reg_type === "new" && strlen($data['firstname']) < 2 ) {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Too short firstname: '.$data['firstname']));
    die();
}
if( $reg_type === "new" && strlen($data['lastname']) < 2 ) {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Too short lastname: '.$data['lastname']));
    die();
}

/* Validate optional purchase_date */
if( isset($data['purchased']) ) {
    $purchased = clean_date($data['purchased']) ;
    if($purchased === false) {
        set_response_code(400);
        echo $crypt->json_encode_and_encrypt(array('error' => 'Could not parse date from field purchased: '.$data['purchased']));
        die();
    }
    $data['purchased'] = $purchased;
}

/* Validate optional source */
if( isset($data['source']) && !in_array($data['source'], array('snapporder', 'sms', 'manual')) ) {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Invalid value in field source: '.$data['source']));
    die();
}

/* Validate optional membership_trial */
if( isset($data['membership_trial']) && $data['membership_trial'] !== "buddy") {
    set_response_code(400);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Invalid value in field membership_trial: '.$data['membership_trial']));
    die();
}

/* Create user */
try {
    if( $reg_type === "new" ) {
        $user_id = add_user($data, "snapporder");
    } else {
        renew_user($data);
    }
} catch(InsideDatabaseException $e) {
    set_response_code(500);
    echo $crypt->json_encode_and_encrypt(array('error' => 'db_error', 'error_message' => $e->getMessage()));
    error_log($e->getMessage());
    die();
}

/* Get and format user */
$user = NULL;
try {
    $user = get_user($user_id);
} catch(InsideDatabaseException $e) {
    set_response_code(500);
    echo $crypt->json_encode_and_encrypt(array('error' => 'db_error', 'error_message' => $e->getMessage()));
    error_log($e->getMessage());
    die();
}

/* Add register url */
if($user['registration_status'] === "partial") {
    $user['registration_url'] = generate_registration_url($user, SECRET_KEY);
}

/* Add back phone number from query */
$user['phone'] = $data['phone'];

if( isset($data['membership_trial']) ) {
    /* Add back membership_trial from query */
    $user['membership_trial'] = $data['membership_trial'];
}

/* Send welcome email */
if($reg_type === "new") {
    send_activation_email($data, $user);
}

/* Return encrypted user object */
echo $crypt->json_encode_and_encrypt($user);
 
?>
