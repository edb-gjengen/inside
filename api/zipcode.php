<?php
set_include_path("../includes/");
require_once("../inside/credentials.php");
require_once("../includes/DB.php");

/* Functions */

/* Set HTTP response code */
function set_response_code($code) {
    if(!is_int($code)) {
        return false;
    }
    header('X-Ignore-This: something', true, $code);
}

function is_valid_utf8($text) {
    return mb_check_encoding($text, 'utf-8');
}

header('Access-Control-Allow-Origin: *', true); // Come fetch!

/* Connect to database */
$options = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
);

$conn = DB::connect(getDSN(), $options);

if(DB :: isError($conn)) {
    echo $conn->toString();
}
$conn->setFetchMode(DB_FETCHMODE_ORDERED);

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
