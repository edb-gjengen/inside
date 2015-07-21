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
if(count($res) == 0) {
    echo json_encode(array('meta' => array('num_results' => 0),'results' => []));
    die();
}
$id_array = array();
foreach($res as $value) {
    $id_array[] = $value[0];
}
$user_ids = implode(",", $id_array);

try{
    $results = get_user_data($user_ids);
} catch(Exception $e) {
    set_response_code(500);
    echo json_encode(array('error' => $e->getMessage()));
    die();
}
echo json_encode(array(
    'meta' => array(
        'num_results' => count($results)
    ),
    'results' => $results,
));

?>