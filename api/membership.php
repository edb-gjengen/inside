<?php

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

$required_keys = array('apikey', 'user_id');
$valid_keys = $required_keys;
$valid_keys = array_merge($valid_keys, array('purchased', 'source'));

$valid_sources = array('kassa');

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
/* Validate User id */
if(strlen($data['user_id']) == 0) {
    return_json_response(array('error' => "Param user_id can not be empty: '".var_export($data['user_id'], true)."'."), 400);
}
if( !is_numeric($data['user_id']) ) {
    return_json_response(array('error' => "Param user_id must be a number: '".$data['user_id']."'."), 400);
}

$conn = get_db_connection(DB_FETCHMODE_ASSOC);

/* Existing user? */
$user = get_user($data['user_id']);
/* Don't allow renewal if valid membership exists. */
if( $user['membership_status'] !== 0 ) {
    if($user['expires'] === '') {
        // Life long
        return_json_response(array('error' => 'Cannot renew, user with id '.$user['id'].' has a life long membership.'), 409);
    }
    return_json_response(array('error' => 'Cannot renew, user with phone '.$user['id'].' has a valid membership until: '.$user['expires']), 409);
}
$first_user_membership = $user['expires'] === '0000-00-00';

/* Validate optional purchase_date */
$purchased = NULL;
if( isset($data['purchased']) ) {
    $purchased = clean_date($data['purchased']);
    if($purchased === false) {
        return_json_response(array('error' => 'Could not parse date from field purchased: '.$data['purchased']), 400);
    }
}

/* Validate optional source */
if( isset($data['source']) && !in_array($data['source'], $valid_sources) ) {
    return_json_response(array('error' => 'Invalid value in field source: '.$data['source']), 400);
}

/* Add or renew membership */
try {
    add_or_renew_membership($data['user_id'], $purchased);

    $success_message = "Medlemskap registrert via ".$data['source'].".";
    if(!$first_user_membership) {
        $success_message = "Medlemskap fornyet via ".$data['source'].".";
    }
    log_userupdate($data['user_id'], $success_message);
} catch(InsideDatabaseException $e) {
    error_log($e->getMessage());
    return_json_response(array('error' => 'db_error', 'error_message' => $e->getMessage()), 500);
}

/* Get and format user */
$user = NULL;
try {
    $user = get_user_data($data['user_id']);
} catch(InsideDatabaseException $e) {
    error_log($e->getMessage());
    return_json_response(array('error' => 'db_error', 'error_message' => $e->getMessage()), 500);
}

send_membership_confirmation_mail($user, $first_user_membership);

/* Return encrypted user object */
return_json_response(array('user' => $user) );
