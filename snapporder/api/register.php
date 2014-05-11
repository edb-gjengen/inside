<?php 

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


/* 
 * Registers a user in the user table
 *
 * Request:
 *
 * $ curl /snapporder/api/register.php 
 * {
 *   "phone":12345678,
 *   "firstName": "Jon",
 *   "lastName": "Hansen",
 *   "email": "jon@uio.no"
 * }
 *
 * Response:
 *
 * {
 *   "phone":12345678,
 *   "memberStatus":1,
 *   "endDate": "2015-04-23" ,
 *   "membershipNumber": "dsadsa2333",
 *   "firstName": "Jon",
 *   "lastName": "Hansen",
 *   "email": "jon@uio.no"
 * }
 *
 * TODO:
 * - register user with fromSnappOrder flag
 * - return result
 */

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

foreach($required_keys as $key) {
    if(!array_key_exists($key, $data)) {
        set_response_code(400);
        echo json_encode(array('error' => 'missing required field \''.$key.'\''));
        die();
    }
}
// TODO validate phonenumber
// TODO validate email
// TODO look at getUseridFromPhone in inside/functions.php
// TODO when $fromSnappOrder is sent to User-constructor...
// ...then work around requirements for username, password, birthdate, address 

/* Create user */
//$new_user = new User(NULL, $data);

/* Get and format user */
//$user = get_user($new_user->id);

/* Add back phone number from query */
//$user['phone'] = $phone;

/* Return encrypted user object */
//echo json_encode($user);
//echo $crypt->encrypt(json_encode($user));
 
?>
