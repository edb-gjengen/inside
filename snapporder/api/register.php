<?php 
/* 
 * Registers a user in the user table
 *
 * Request:
 *
 * $ curl /snapporder/api/register.php 
 * {
 *   "phone": "+4742345678",
 *   "firstname": "Jon",
 *   "lastname": "Hansen",
 *   "email": "jon@uio.no",
 *   "purchased": "2004-02-12"  // optional, format: ISO-8601 date
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
 *   "registration_status": "partial"  // "partial" means show link
 *   "register_url": "/snapporder/register_partial.php?id=4331&hmac=lol"
 * }
 *
 * TODO Add entry in din_userupdate
 * TODO maybe check $_SERVER['REQUEST_METHOD']
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
// TODO enable
//$crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);
//$body = $crypt->decrypt($body);
$data = (array) json_decode($body);
if($data === NULL) {
    set_response_code(400);
    echo json_encode(array('error' => 'Can\'t decode body'));
    die();
}

/* Validate supplied data */
$required_keys = array('firstname', 'lastname', 'phone', 'email');
$valid_keys = $required_keys;
$valid_keys[] = + 'purchased';

foreach($required_keys as $key) {
    if(!array_key_exists($key, $data)) {
        set_response_code(400);
        echo json_encode(array('error' => 'missing required field \''.$key.'\''));
        die();
    }
}
foreach($data as $key => $value) {
    if(!in_array($key, $valid_keys)) {
        set_response_code(400);
        echo json_encode(array('error' => 'unknown field \''.$key.'\''));
        die();
    }
}

$data['phone'] = clean_phonenumber($data['phone']);
if( !valid_phonenumber($data['phone']) ) {
    set_response_code(400);
    echo json_encode(array('error' => 'Not a phone number:'.$data['phone']));
    die();
}
if( !valid_email($data['email']) ) {
    set_response_code(400);
    echo json_encode(array('error' => 'Not an email: '.$data['email']));
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

/* Existing user? */
if( getUseridFromEmail($data['email']) !== false) {
    set_response_code(409);
    echo json_encode(array('error' => 'Existing user with email: '.$data['email']));
    die();
}
if( getUseridFromPhone($data['phone']) !== false ) {
    set_response_code(409);
    echo json_encode(array('error' => 'Existing user with phone: '.$data['phone']));
    die();
}

/* validate firstname and lastname */
if( strlen($data['firstname']) < 2) {
    set_response_code(400);
    echo json_encode(array('error' => 'Too short firstname: '.$data['firstname']));
    die();
}
if( strlen($data['lastname']) < 2) {
    set_response_code(400);
    echo json_encode(array('error' => 'Too short lastname: '.$data['lastname']));
    die();
}

/* Validate optional purchase_date */
if( isset($data['purchased']) ) {
    $purchased = clean_date($data['purchased']) ;
    if($purchased === false) {
        set_response_code(400);
        echo json_encode(array('error' => 'Could not parse date from field purchased: '.$data['purchased']));
        die();
    }
    $data['purchased'] = $purchased;
}


/* Create user */
$user_id = add_user($data);

/* Get and format user */
$user = get_user($user_id);

/* Add back phone number from query */
$user['phone'] = $data['phone'];

/* Return encrypted user object */
echo json_encode($user);
//echo $crypt->encrypt(json_encode($user));
 
?>
