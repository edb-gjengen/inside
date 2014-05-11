<?php
/* Init */
set_include_path("../includes/");
require_once("../inside/credentials.php");
require_once("../includes/DB.php");
/* Connect db */
$options = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
);
$db = DB::connect(getDSN(), $options);
if(DB :: isError($conn)) {
    echo $conn->toString();
}
$db->setFetchMode(DB_FETCHMODE_ASSOC);


?>
