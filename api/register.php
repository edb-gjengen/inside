<?php 
/* 
 * Registers a user in the user table
 *
 * Request (new):
 *
 * $ curl /api/register.php
 * {
 *   "phone": "+4742345678",
 *   "purchased": "2004-02-12"    // optional, format: ISO-8601 date
 *   "source": "physical"             // optional, possible values: "physical", "web" or "manual"
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
 * $ curl /api/register.php
 * {
 *   "phone": "+4742345678",
 *   "purchased": "2004-02-12"   // optional, format: ISO-8601 date
 *   "type": "renewal"           // optional, possible values: "renewal"
 *   "source": "physical"  // optional, possible values: "physical", "web" or "manual"
 * }
 *
 */

/* Pull in Inside */
set_include_path("../includes/".PATH_SEPARATOR."../inside/".PATH_SEPARATOR."../snapporder");
require_once("functions.php");
require_once("User.php");

/* Pull in some nifty stuff from snapporder integration */
require_once("api_functions.php");
require_once("lib/functions.php");
require_once("config.php");

/* Validate request */
$body = file_get_contents('php://input');
if(strlen($body) === 0) {
    return_json_response(array('error' => 'Invalid request'), 400);
}

/* Decode body */
$data = json_decode($body, true);
if($data === NULL) {
    return_json_response(array('error' => 'Can\'t decode body'), 400);
}
/* Check type of registration and set valid fields */
$reg_type = "new";
if( isset($data['type']) && $data['type'] === "renewal" ) {
    /* Renewal */
    $reg_type = $data['type'];
}

$required_keys = array('apikey', 'phone');
$valid_keys = $required_keys;
$valid_keys = array_merge($valid_keys, array('purchased', 'source', 'type', 'membership_trial', 'cardno'));

$valid_sources = array('physical', 'web', 'manual');

/* Validate supplied data */
foreach($required_keys as $key) {
    if(!array_key_exists($key, $data)) {
        return_json_response(array('error' => 'missing required field \''.$key.'\''), 400);
    }
}
foreach($data as $key => $value) {
    if(!in_array($key, $valid_keys)) {
        return_json_response(array('error' => 'unknown field \''.$key.'\''), 400);
    }
}

/* Valid API KEY (defined in credentials.php) ? */
if( $data['apikey'] !== USER_API_KEY_KASSA ) {
    return_json_response(array('error' => "Invalid apikey: '".$data['apikey']."'."), 400);
}

$data['phone'] = clean_phonenumber($data['phone']);
if( !valid_phonenumber($data['phone']) ) {
    return_json_response(array('error' => "Not a phone number: '".$data['phone']."'"), 400);
}

$conn = get_db_connection(DB_FETCHMODE_ASSOC);

/* Existing user? */
$user_id = getUseridFromPhone($data['phone']);
if( $reg_type === "new" && $user_id !== false ) {
    return_json_response(array('error' => 'Existing user with phone: '.$data['phone']), 409);
}
/* Renewal of membership? */
if( $reg_type === "renewal" && $user_id === false ) {
    return_json_response(array('error' => 'Could not find user with phone: '.$data['phone']), 409);
}

$data['user_id'] = $user_id;
if( $reg_type === "renewal") {
    $user = get_user($user_id);
    /* Don't allow renewal of an existing valid membership. */
    if( $user['membership_status'] !== 0 ) {
        if($user['expires'] === '') {
            // Life long
            return_json_response(array('error' => 'Cannot renew, user with phone '.$data['phone'].' has a life long membership.'), 409);
        }
        return_json_response(array('error' => 'Cannot renew, user with phone '.$data['phone'].' has a valid membership until: '.$user['expires']), 409);
    }
}

/* Validate optional purchase_date */
if( isset($data['purchased']) ) {
    $purchased = clean_date($data['purchased']) ;
    if($purchased === false) {
        return_json_response(array('error' => 'Could not parse date from field purchased: '.$data['purchased']), 400);
    }
    $data['purchased'] = $purchased;
}

/* Validate optional source */
if( isset($data['source']) && !in_array($data['source'], $valid_sources) ) {
    return_json_response(array('error' => 'Invalid value in field source: '.$data['source']), 400);
}

/* Validate optional membership_trial */
if( isset($data['membership_trial']) && $data['membership_trial'] !== "buddy") {
    return_json_response(array('error' => 'Invalid value in field membership_trial: '.$data['membership_trial']), 400);
}

/* Create user */
try {
    if( $reg_type === "new" ) {
        $user_id = add_user($data, $data['source']);
    } else {
        renew_user($data);
    }
} catch(InsideDatabaseException $e) {
    error_log($e->getMessage());
    return_json_response(array('error' => 'db_error', 'error_message' => $e->getMessage()), 500);
}

/* Get and format user */
$user = NULL;
try {
    $user = get_user($user_id);
} catch(InsideDatabaseException $e) {
    error_log($e->getMessage());
    return_json_response(array('error' => 'db_error', 'error_message' => $e->getMessage()), 500);
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
    // TODO: send_activation_sms($data, $user);
}

/* Return encrypted user object */
return_json_response($user);
