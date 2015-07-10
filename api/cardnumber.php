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

$card_number = $_GET['q'];

if( !is_numeric($card_number) ) {
    set_response_code(400);
    echo json_encode(array('error' => "Value q must be numeric: '".$card_number."'"));
    die();
}

$card_number = $conn->quoteSmart($card_number);

// Search query
$sql = "
  SELECT mc.userId FROM din_membercard AS mc
  LEFT JOIN din_user AS u ON mc.userId=u.id
  WHERE mc.id=$card_number";
error_reporting(0);

$res = $conn->getAll($sql);

if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}

/* Invalid card number*/
if(count($res) === 0) {
    echo json_encode(array(
        'user' => NULL,
        'valid' => false
    ));
    die();
}

$user_id = $res[0][0];
/* Free card_number? */
if($user_id === "") {
    echo json_encode(array(
        'user' => NULL,
        'valid' => true
    ));
    die();
}

/* Existing user with card_number. */
try{
    $user_data = get_user_data($user_id, $conn);
} catch(Exception $e) {
    set_response_code(500);
    echo json_encode(array('error' => $e->getMessage()));
    die();
}
echo json_encode(array(
    'user' => $user_data,
    'valid' => $res !== NULL
));

?>