<?php
require_once("api_functions.php");
require_once("../includes/DB.php");


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
    if( !isset($data['user_id']) ) {
        return_json_response(array('error' => "Missing param user_id"), 400);
    }
    if( !is_numeric($data['user_id']) ) {
        return_json_response(array('error' => "Value user_id must be numeric: '".$data['user_id']."'"), 400);
    }
    /* If user does not exist, bail */
    $user_data = get_user_data($data['user_id'], $conn);
    if( count($user_data) !== 1 ) {
        return_json_response(array('error' => "User with user_id '".$data['user_id']."' does not exist."), 400);
    }
    $user = $user_data[0];

    /* If card number is attached to specified user, bail */
    if($user['cardno'] === $data['card_number']) {
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
    /* TODO: Add card relationship OR remove old relationsip and add new */
    return_json_response(array('error' => 'TODO: Not implemented'));
}
else {
    /* Validate params */
    if( !isset($_GET['card_number']) && !isset($_POST['card_number']) ) {
        return_json_response(array('error' => "Missing param card_number: ".var_export($_POST, true)), 400);
    }
    $card_number = $_GET['card_number'];

    if( !is_numeric($card_number) ) {
        return_json_response(array('error' => "Value card_number must be numeric: '".$card_number."'"), 400);
    }

    // Search query
    $card_number = $conn->quoteSmart($card_number);
    $sql = "SELECT mc.userId FROM din_membercard AS mc
      LEFT JOIN din_user AS u ON mc.userId=u.id
      WHERE mc.id=$card_number";

    $res = $conn->getAll($sql);

    if( DB::isError($res) ) {
        return_json_response(array('error' => $res->message), 500);
    }
    $card_number_exists = count($res) === 0;

    /* Invalid card number? */
    if($card_number_exists) {
        return_json_response(array('user' => NULL, 'valid' => false));
    }

    $user_id = $res[0][0];
    /* Free card_number? */
    if($user_id === "") {
        return_json_response(array('user' => NULL, 'valid' => true));
    }

    /* Existing user with card_number. */
    try{
        $user_data = get_user_data($user_id, $conn);
    } catch(Exception $e) {
        return_json_response(array('error' => $e->getMessage()), 500);
        return;  // Note: Help IntelliJ code inspection.
    }

    return_json_response(array('user' => $user_data, 'valid' => $res !== NULL));
}