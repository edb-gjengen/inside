<?php
require_once("inside_functions.php");

$user = new User(getCurrentUser());

$current_username = getCurrentUserName();

/* Check for username availability.
 * - You are the owner of the username (the logged in user).
 * - The username is not allready taken. */
if ( ! user_exists($_GET['username']) || $_GET['username'] == strtolower($current_username)) {
    echo "true";
}
else {
    echo "false";
}

?>
