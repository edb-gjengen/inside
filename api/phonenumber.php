<?php
require_once("api_functions.php");
require_once("../includes/DB.php");


header('Access-Control-Allow-Origin: *', true); // Come fetch!

/* Connect to database */
$conn = get_db_connection(DB_FETCHMODE_ORDERED);


if( !isset($_GET['apikey']) ) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing param apikey."));
    die();
}
/* Valid API KEY (defined in credentials.php) ? */
if( $_GET['apikey'] !== USER_API_KEY && $_GET['apikey'] !== USER_API_KEY_KASSA ) {
    set_response_code(400);
    echo json_encode(array('error' => "Invalid apikey: '".$_GET['apikey']."'."));
    die();
}

/* Validate params */
if(!isset($_GET['q'])) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing param q"));
    die();
}

$phonenumber = $_GET['q'];
$phonenumber = clean_phonenumber($phonenumber);
if(!valid_phonenumber($phonenumber)) {
    set_response_code(400);
    echo json_encode(array('error' => "Invalid phone number: '".$phonenumber."'"));
    die();
}
$phonenumber = $conn->quoteSmart($phonenumber);

// Search query
$sql = "
  SELECT u.id FROM din_userphonenumber AS p
  LEFT JOIN din_user AS u ON p.user_id=u.id
  WHERE p.number=$phonenumber";

$res = $conn->getAll($sql);

if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}
$user_ids = array();
if(count($res) == 0) {
    echo json_encode(array('results' => []));
    die();
}
$user_ids = $res[0];
echo json_encode(array('results' => $user_ids));

?>