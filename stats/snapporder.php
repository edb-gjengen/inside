<?php
require('../api/api_functions.php');

header('Access-Control-Allow-Origin: *', true); // Come fetch!

$conn = get_db_connection(DB_FETCHMODE_ASSOC);

$is_start = isset($_GET['start']); # TODO checking

$start_date = $is_start ? $_GET['start'] : '2015-08-01';
$start_date = $conn->quoteSmart($start_date);
$sql = "SELECT DATE_FORMAT(date, '%Y-%m-%d') AS date, count(*) AS sales
        FROM din_userupdate
        WHERE date > $start_date
        AND (comment='Medlemskap registrert via snapporder.'
          OR comment='Medlemskap fornyet via snapporder.')
        GROUP BY DATE_FORMAT(date, '%Y-%m-%d')
        ORDER BY date";

$rows = $conn->getAll($sql);
if( DB::isError($rows) ) {
    return_json_response( array('result' => 'db_error') );
}
return_json_response(array('memberships'=> $rows, 'meta' => array('sql' => $sql)));