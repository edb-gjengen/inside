<?php
// TODO: limit access by API-key
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

if( !isset($_GET['apikey']) ) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing param apikey."));
    die();
}
/* Valid API KEY (defined in credentials.php) ? */
if( $_GET['apikey'] !== USER_API_KEY ) {
    set_response_code(400);
    echo json_encode(array('error' => "Invalid apikey.".$_GET['apikey']));
    die();
}

/* Validate params */
if(!isset($_GET['q'])) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing search param q"));
    die();
}

/* Filters */
$groups = array();
if(isset($_GET['filter_groups']) && strlen($_GET['filter_groups']) > 0 ) {
    if( !strstr(",", $_GET['filter_groups']) ) {
        $groups = array($_GET['filter_groups']);
    } else {
        $groups = explode($_GET['filter_groups'], ",");
    }
    foreach($groups as $group) {
        if( !is_numeric($group) ) {
            set_response_code(400);
            echo json_encode(array('error' => "Value in filter_groups must be numeric: '".$group."'"));
            die();
        }
    }
}

// TODO
//  - define a set of admin-groups (admin=1) and use in group filter
//  - allow new param is_member

if( strlen($_GET['q']) <= 2 && count($groups) == 0 ) {
    set_response_code(400);
    echo json_encode(array('error' => "Search param must be longer than 2 chars"));
    die();
}


$query = $_GET['q'];

// Add wildcards around and between words
$query = "%" . str_replace(" ", "% %", $query) . "%";
$query = $conn->quoteSmart($query);


// Search query
$user_search_query = "CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
        OR CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
        OR UPPER(u.username) LIKE $query
        OR UPPER(u.email) LIKE $query";

// Default
$sql = "SELECT DISTINCT u.id
    FROM din_user u
    WHERE $user_search_query";

// Overrid query with groups 
if(count($groups) > 0) {
    $sql = "SELECT DISTINCT u.id
        FROM din_user u
        LEFT JOIN din_usergrouprelationship as ug ON u.id=ug.user_id
        WHERE ($user_search_query)
        AND ug.group_id IN(".implode($groups, ",").")";
}

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
$sql_is_member = "u.expires > NOW() OR u.expires IS NULL AS is_member";
$sql_groups = "GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') AS groups";
$sql = "SELECT u.id,u.username,u.firstname,u.lastname,u.email,up.number,$sql_groups,$sql_is_member
    FROM din_user as u
    LEFT JOIN din_userphonenumber as up ON up.user_id=u.id
    LEFT JOIN din_usergrouprelationship AS ug ON u.id=ug.user_id
    LEFT JOIN din_group AS g ON g.id=ug.group_id
    WHERE u.id IN ($ids)
    GROUP BY u.id
    ORDER BY u.firstname, u.lastname ASC";

$res = $conn->getAll($sql);

if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->getMessage()));
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
