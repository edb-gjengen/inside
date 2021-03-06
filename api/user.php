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
/* Note: Only gives kassa.neuf.no API-rights to search all members */
$limit_to_active = $_GET['apikey'] !== USER_API_KEY_KASSA;

/* Validate params */
if(!isset($_GET['q'])) {
    set_response_code(400);
    echo json_encode(array('error' => "Missing search param q"));
    die();
}

/* Filters */
$groups = array();
if(isset($_GET['filter_groups']) && strlen($_GET['filter_groups']) > 0 ) {
    if( !strstr($_GET['filter_groups'], ",") ) {
        $groups[] = $_GET['filter_groups'];
    } else {
        $groups = explode(",", $_GET['filter_groups']);
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
$filter_valid_membership = false;
if( isset($_GET['has_valid_membership']) && $_GET['has_valid_membership'] == "true") {
    $filter_valid_membership = true;
}

if( strlen($_GET['q']) <= 2 && count($groups) == 0 && !$filter_valid_membership ) {
    set_response_code(400);
    echo json_encode(array('error' => "Search param must be longer than 2 chars"));
    die();
}


$query = $_GET['q'];

if( !isset($_GET['exact']) ) {
    // Add wildcards around and between words
    $query = "%" . str_replace(" ", "% %", $query) . "%";
}
$query = $conn->quoteSmart($query);


// Search query
// TODO allow users with permission to search all users (not filter active group)
// TODO: group memberships should be "AND-ed" together somehow
$user_search_query = "CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
        OR CONCAT(UPPER(u.firstname), ' ', UPPER(u.lastname)) LIKE $query
        OR UPPER(u.username) LIKE $query
        OR UPPER(u.email) LIKE $query";

if( isset($_GET['exact']) ) {
    $user_search_query = "u.username=$query";
}

$valid_membership_query = $filter_valid_membership ? " AND (u.expires >= NOW() OR u.expires IS NULL)" : "";
$limit_active_query = $limit_to_active ? "AND ug.group_id=2" : "";  // Note: Conditional filter on active group

// Default
$sql = "SELECT DISTINCT u.id
    FROM din_user u
    LEFT JOIN din_usergrouprelationship as ug ON u.id=ug.user_id
    WHERE ($user_search_query)
    $limit_active_query
    $valid_membership_query";

// Override query with groups
if(count($groups) > 0 && $limit_to_active) {
    $sql = "SELECT DISTINCT u.id
        FROM din_user u
        LEFT JOIN din_usergrouprelationship as ug ON u.id=ug.user_id
        LEFT JOIN din_usergrouprelationship as ug2 ON u.id=ug2.user_id
        WHERE ($user_search_query)
        AND ug.group_id IN(".implode($groups, ",").")
        AND ug2.group_id=2  /* Note: Always filter on active group, here with a double join */
        $valid_membership_query";
} elseif(count($groups) > 0 && !$limit_to_active) {
    $sql = "SELECT DISTINCT u.id
        FROM din_user u
        LEFT JOIN din_usergrouprelationship as ug ON u.id=ug.user_id
        WHERE ($user_search_query)
        AND ug.group_id IN(".implode($groups, ",").")
        $valid_membership_query";
}

$res = $conn->getAll($sql);
if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->message));
    die();
}

if( count($res) === 0 ) {
    echo json_encode(array(
        'meta' => array('num_results' => 0, 'query' => $sql),
        'results' => array()
    ));
    die();
}
$id_array = array();
foreach($res as $value) {
    $id_array[] = $value[0];
}
$ids = implode(",", $id_array);

try{
    $results = get_user_data($ids);
} catch(Exception $e) {
    set_response_code(500);
    echo json_encode(array('error' => $e->getMessage()));
    die();
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