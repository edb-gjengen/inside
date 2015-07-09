<?php
require_once("api_functions.php");
require_once("../includes/DB.php");


header('Access-Control-Allow-Origin: *', true); // Come fetch!

$conn = get_db_connection(DB_FETCHMODE_ORDERED);
error_reporting(0);

/* Validate params */
if(!isset($_GET['q'])) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing zipcode param q"));
    die();
}
if( strlen($_GET['q']) > 4 ) {
    // Norwegian zipcodes are not longer than 4 digits
    echo json_encode(array('result' => ""));
    die();
}

$query = $_GET['q'];
$query = $conn->quoteSmart($query);

// Search query
$sql = "SELECT p.kommunenavn FROM din_postnummer p WHERE p.postnummer=$query";

$res = $conn->getAll($sql);
if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}

if( count($res) === 1 ) {
    echo json_encode(array(
        'result' => $res[0][0]
    ));
} else {
    echo json_encode(array(
        'result' => ""
    ));
}
?>
