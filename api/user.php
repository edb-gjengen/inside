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
    echo json_encode(array('error' => "Missing search param q"));
    die();
}
if( strlen($_GET['q']) <= 2 ) {
    set_response_code(400);
    echo json_encode(array('error' => "Search param must be longer than 2 chars"));
    die();
}

$query = $_GET['q'];

// Add wildcards around and between words
$query = "%" . str_replace(" ", "% %", $query) . "%";
$query = $conn->quoteSmart($query);

// Search query
$sql = "SELECT DISTINCT u.id
    FROM din_user u
    WHERE CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
    OR CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
    OR UPPER(u.username) LIKE $query
    OR UPPER(u.email) LIKE $query";

$res = $conn->getAll($sql);
if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}

if( count($res) === 0 ) {
    echo json_encode(array(
        'meta' => array('num_results' => 0),
        'results' => array()
    ));
    die();
}
$id_array = array();
foreach($res as $value) {
    $id_array[] = $value[0];
}
$ids = implode(",", $id_array);

$conn->setFetchMode(DB_FETCHMODE_ASSOC);
/* Get data */
$sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, up.number
    FROM din_user as u
    LEFT JOIN din_userphonenumber as up ON up.user_id=u.id
    WHERE u.id IN ($ids)
    ORDER BY u.firstname, u.lastname ASC";

$res = $conn->getAll($sql);

if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}


/* Encode and output */
$results = array();

foreach($res as $result) {
    foreach($result as $key => $value) {
        /* Encoding issues? oh yes, utf-8 please */
        if(!is_valid_utf8($value)) {
            $result[$key] = utf8_encode($value);
        }
    }
    $results[] = $result;
}
$out = json_encode(array(
    'meta' => array(
        'num_results' => count($results),
        'query' => $sql),
    'results' => $results,
));

if($out === false) {
    set_response_code(500);
    echo json_encode(array('error' => json_last_error_msg()));
    die();
}
echo $out;
?>
