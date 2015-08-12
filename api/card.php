<?php
set_include_path("../includes/");
require_once("../inside/functions.php");
require_once("api_functions.php");


header('Access-Control-Allow-Origin: *', true); // Come fetch!

/* Connect to database */
$conn = get_db_connection(DB_FETCHMODE_ORDERED);


if( !isset($_GET['apikey']) ) {
    return_json_response(array('error' => "Missing param apikey."), 400);
}
/* Valid API KEY (defined in credentials.php) ? */
if( $_GET['apikey'] !== USER_API_KEY && $_GET['apikey'] !== USER_API_KEY_KASSA ) {
    return_json_response(array('error' => "Invalid apikey: '".$_GET['apikey']."'."), 400);
}
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    /* Validate params */
    if( !isset($data['card_number']) ) {
        return_json_response(array('error' => "Missing param card_number"), 400);
    }
    $card_number = $data['card_number'];

    if( !is_numeric($card_number) ) {
        return_json_response(array('error' => "Value card_number must be numeric: '".$card_number."'"), 400);
    }

    /* Validate user_id */
    if( !isset($data['user_id']) || !isset($data['phone_number']) ) {
        return_json_response(array('error' => "Missing either param user_id or phone_number"), 400);
    }

    /* Validate action */
    $valid_actions = array('new_card_membership', 'update_card', 'add_or_renew', 'sms_card_notify');
    if( !isset($data['action']) || !in_array($data['action'], $valid_actions) ) {
        return_json_response(array('error' => "Missing param 'action' or action not in ".implode(", ", $valid_actions).'.'), 400);

    }

    /* New membership or SMS membership, with only card number and phone number tuple */
    if( in_array($data['action'], array('new_card_membership', 'sms_card_notify')) ) {
        $phone_number = clean_phonenumber($data['phone_number']);
        if(!valid_phonenumber($phone_number)) {
            return_json_response(array('error' => "Invalid phone number: '".$phone_number."'"), 400);
        }
        $card = get_card($card_number);
        if($card['registered'] !== '') {
            return_json_response(array('error' => 'Card number is in use and belongs to phone number: '.$card['owner_phone_number'].'.'), 400);
        }
        /* If user with phone number exists, then bail */
        $user_id = getUseridFromPhone($phone_number);
        if( $user_id !== false ) {
            return_json_response(array('error' => 'User with phone number: '.$phone_number.' already exists: '.$user_id.'.'), 400);
        }

        update_card_with_phone_number($card_number, $phone_number);

        /* Return fresh card object */
        return_json_response(array('user'=> NULL, 'card' => get_card($card_number)));

    }
    else if( in_array($data['action'], array('update_card', 'add_or_renew')) ) {
        /* Existing user */
        if( !is_numeric($data['user_id']) ) {
            return_json_response(array('error' => "Value user_id must be numeric: '".$data['user_id']."'"), 400);
        }
        /* If user does not exist, bail */
        $user_data = get_user_data($data['user_id']);
        if( count($user_data) !== 1 ) {
            return_json_response(array('error' => "User with user_id '".$data['user_id']."' does not exist."), 400);
        }
        $user = $user_data[0];
        $active_card_number = get_active_card_number($user['cards']);

        /* If card number is attached to specified user, bail */
        if($active_card_number && $active_card_number === $data['card_number']) {
            return_json_response(array('error' => "Card number ".$data['card_number']." is already attached to user (".$data['user_id'].")."), 400);
        }
        /* If card number does not exist, bail */
        $id_from_card_number = get_user_id_by_card_number($data['card_number']);
        if($id_from_card_number === NULL) {
            return_json_response(array('error' => "Card number ".$data['card_number']." does not exist."), 400);
        }
        /* If card belongs to another user, bail */
        if($id_from_card_number !== "") {
            return_json_response(array('error' => "Card number ".$data['card_number']." belongs to another user ($id_from_card_number)."), 400);
        }

        /* Add card relationship OR set old relationship inactive and add new */
        update_card($data['user_id'], $data['card_number']);

        log_userupdate($data['user_id'], "Kortnummer ".$data['card_number']." knyttet til bruker.");

        return_json_response(array('user' => get_user_data($data['user_id'])));
    }

    return_json_response(array('error' => 'Unknown action, gave up.'), 400);
}
else {
    /* Validate params */
    if( !isset($_GET['card_number']) ) {
        return_json_response(array('error' => "Missing param card_number."), 400);
    }
    $card_number = $_GET['card_number'];

    if( !is_numeric($card_number) ) {
        return_json_response(array('error' => "Value card_number must be numeric: '".$card_number."'"), 400);
    }

    $card = get_card($card_number);

    if(!$card || $card['user_id'] === "") {
        $user = NULL;
    } else {
        /* Existing user with card_number */
        try{
            $user = get_user_data($card['user_id'])[0];
        } catch(Exception $e) {
            return_json_response(array('error' => $e->getMessage()), 500);
            return;  // Note: Help IntelliJ code inspection.
        }
    }

    return_json_response(array('user' => $user, 'card' => $card));
}