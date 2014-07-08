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

/* Connect to database */
$options = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
);

$conn = DB::connect(getDSN(), $options);

if(DB :: isError($conn)) {
    echo $conn->toString();
}
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

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

$sql = "SELECT DISTINCT u.id, u.username, u.firstname, u.lastname, u.email
    FROM din_user u
    WHERE CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
    OR CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
    OR UPPER(u.username) LIKE $query
    OR UPPER(u.email) LIKE $query
    ORDER BY u.firstname, u.lastname ASC";

$res = $conn->query($sql);
if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->toString));
    die();
}
    
$results = array();
while ($row =& $res->fetchRow()) {
    $results[] = $row;
}
//var_dump($results);
$out = json_encode(array(
    'results' => $results,
    'meta' => array(
        'num_results' => $res->numRows(),
        'query' => $sql
    )));
if($out === false) {
    set_response_code(500);
    echo json_encode(array('error' => json_last_error_msg()));
    die();
}
echo $out;
?>
