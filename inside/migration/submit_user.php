<?php
require_once("inside_functions.php");
require_once("ldap_api_functions.php");

/* Sanity checks. */
if( !( isset($_POST['username']) || isset($_POST['password']) || isset($_POST['password_check'])) ) {
    die( json_encode( Array('errors' => Array( "non_field_error" => "All fields are required."))));
}
/* Server side field validation */
$errors = Array();
/* Validate username */
if( strlen($_POST['username']) < 3 && strlen($_POST['username']) > 12 ) {
    $result['errors']['username'][] = "Please enter a username between 3 and 12 characters long.";
}
if( !preg_match("/[a-z]/", $_POST['username']) ) {
    $result['errors']['username'][] = "Please enter only lowercase letters (english alphabet).";
}
if( file_get_contents("./username_available.php") == "false" ) {
    $result['errors']['username'][] = "Username taken.";
}
/* Validate password */
if( strlen($_POST['password']) < 8 ) {
    $result['errors']['password'][] = "Please enter a least 8 characters.";
}
if ($_POST['password'] != $_POST['password_check']) {
    $result['errors']['password'][] = "Passwords do not match.";
}

if ( !isset($result) ) {
    /* update user */
    $updated = update_user($_POST['username'], $_POST['password']);
    if($updated) {
        /* find user info */
        $user = new User(getCurrentUser());
        /* find groups */
        $groups = find_groups(getCurrentUser());

        /* migrated? */
        $migrated = ldap_add_user($_POST['username'], $user->firstname, $user->lastname, $user->email, $_POST['password'], $groups);
        /* Note: Assume success every time, but log result of migrated anyway. */
        _log($migrated);
        set_migrated(getCurrentUser());
        echo json_encode( Array('result' => 'success', 'groups' => $groups) );
    } else {
        echo json_encode( Array('result' => 'error', 'errors' => Array('database' => 'User not migrated.')) );
    }
}
else {
    echo json_encode($result);
}
?>
