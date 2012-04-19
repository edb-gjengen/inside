<?php
set_include_path("../includes/");
require_once("../inside/credentials.php");
require_once("../includes/DB.php");
/* Connect db */
$options = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
);

$conn = DB::connect(getDSN(), $options);

if(DB :: isError($conn)) {
    echo $conn->toString();
}
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

if(isset($_GET['v']) && $_GET['v'] == 'num') {
        echo num_members();
} else if(isset($_GET['v']) && $_GET['v'] == 'reg') {
        echo registrations();
} else if(isset($_GET['v']) && $_GET['v'] == 'reg_grouped') {
        echo members_by_month_and_year();
} else {
        echo json_encode(array('error' => 'Try ?v=num|reg|reg_grouped :-D'));
}

/* current number of members */
function num_members() {
    global $conn;
    $sql = "SELECT COUNT(*) FROM din_user WHERE expires > NOW() OR expires IS NULL;";
    $rows = $conn->getAll($sql);
    if( DB::isError($rows) ) {
       return json_encode( array('result' => 'db_error') );
    }
    return format($rows);
}

/* registrations by month and year */
function registrations() {
    global $conn;
    $sql = "SELECT DATE_FORMAT(din_userupdate.date, '%Y-%m') AS month_and_year, count(*) as num
    FROM  `din_user` , din_userupdate
    WHERE din_user.id = user_id_updated
    AND din_userupdate.comment =  'User registered.'
    GROUP BY DATE_FORMAT(din_userupdate.date, '%Y-%m')
    ORDER BY din_userupdate.date";
    $rows = $conn->getAll($sql);
    if( DB::isError($rows) ) {
       return json_encode( array('result' => 'db_error') );
    }
    return format($rows);
}

/* Current members by first registered month and year */
function members_by_month_and_year() {
    global $conn;
    $sql = "SELECT DATE_FORMAT(din_userupdate.date, '%Y-%m') AS month_and_year, count(*) as num
    FROM  `din_user`,din_userupdate
    WHERE expires > NOW()
    AND din_user.id = user_id_updated
    AND din_userupdate.comment = 'User registered.'
    GROUP BY DATE_FORMAT(din_userupdate.date, '%Y-%m')
    ORDER BY din_userupdate.date;";
    $rows = $conn->getAll($sql);
    if( DB::isError($rows) ) {
       return json_encode( array('result' => 'db_error') );
    }
    return format($rows);
}

function format($rows) {
    $result = array();
    foreach($rows as $row) {
        $tmp = array();
        foreach($row as $k => $v) {
            $tmp[] = $v;
        }
        $result[] = $tmp;
    }

    /* json_encode */
    $result = json_encode( array( 'result' => $result) );

    return $result;
}

?>
