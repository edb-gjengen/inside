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

/* Validate params */
if(!isset($_GET['q'])) {
    return_json_response(array('error' => "Missing param q"), 400);
}

$phonenumber = $_GET['q'];
$phonenumber = clean_phonenumber($phonenumber);
if(!valid_phonenumber($phonenumber)) {
    return_json_response(array('error' => "Invalid phone number: '".$phonenumber."'"), 400);
}
$card = get_card_by_phone_number($phonenumber);
$phonenumber = $conn->quoteSmart($phonenumber);

// Search query
$sql = "
  SELECT u.id FROM din_userphonenumber AS p
  LEFT JOIN din_user AS u ON p.user_id=u.id
  WHERE p.number=$phonenumber";

$res = $conn->getAll($sql);

if( DB::isError($res) ) {
    return_json_response(array('error' => $res->message), 500);
}
if(count($res) == 0) {
    return_json_response(array('card' => $card, 'users' => []));
}
$id_array = array();
foreach($res as $value) {
    $id_array[] = $value[0];
}
$user_ids = implode(",", $id_array);

try{
    $results = get_user_data($user_ids);
} catch(Exception $e) {
    return_json_response(array('error' => $e->getMessage()), 500);
    return;  // Note: Makes IDE happy
}
return_json_response(array(
    'users' => $results,
    'card' => $card
));