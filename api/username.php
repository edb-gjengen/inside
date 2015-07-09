<?php
require_once("api_functions.php");
require_once("../includes/DB.php");


header('Access-Control-Allow-Origin: *', true); // Come fetch!

/* Connect to database */
$conn = get_db_connection(DB_FETCHMODE_ORDERED);

/* Validate params */
if(!isset($_GET['q'])) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing param q"));
    die();
}

$query = $_GET['q'];
$query = $conn->quoteSmart($query);

// Search query
$sql = "SELECT id FROM din_user WHERE username=$query OR ldap_username=$query";
$res = $conn->query($sql);

if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}
$userExists = $res->numRows() > 0;

echo json_encode(array('result' => $userExists));

?>
