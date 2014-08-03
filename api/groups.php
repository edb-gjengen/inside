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
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

// Search query
$sql = "SELECT g.name as group_name, g.id as group_id, g.posix_group, g.mailinglist, g.admin as is_admin_group,
    d.user_id_contact, d.nicename, d.office, d.url, d.updated, d.name as division_name, d.id as division_id
    FROM din_division AS d
    LEFT JOIN din_group AS g ON d.id=g.division_id
    WHERE g.id IS NOT NULL
    ORDER BY g.name";
$res = $conn->getAll($sql);

/* Encode */
$results = array();
foreach($res as $result) {
    foreach($result as $key => $value) {
        /* Encoding issues? oh yes, utf-8 please */
        if(is_string($value)) {
            $result[$key] = utf8_encode($value);
        }
    }
    $results[] = $result;
}

if( DB::isError($res) ) {
    set_response_code(500);
    echo json_encode(array('error' => $res->getMessage().". DEBUG: ".$res->getDebugInfo()));
    die();
}
echo json_encode(array('results' => $results));

?>
