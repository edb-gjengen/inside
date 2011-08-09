<?php
require_once("inside_functions.php");

if ( user_exists($_GET['username']) ) {
    echo "true";
}
else {
    echo "false";
}
?>
