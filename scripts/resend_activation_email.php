<?php
/**
 * Resend activation email which contains the user activation.
 *
 * Usage:
 * $ php resend_activation_email.php USER_ID
 *
 **/
set_include_path("../includes/");
set_include_path("../inside/");
require_once("functions.php");
require_once("User.php");
set_include_path("../includes/");
require_once("DB.php");

require_once("../snapporder/config.php");
require_once("../snapporder/lib/functions.php");


/* Connect to database */
$options = array( 'debug' => 2, 'portability' => DB_PORTABILITY_ALL );
$conn = DB::connect(getDSN(), $options);
if(DB :: isError($conn)) {
    echo $conn->toString();
    set_response_code(500);
    echo $crypt->json_encode_and_encrypt(array('error' => 'Could not connect to DB'));
    die();
}
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

if( !defined("CLI_SERVER_NAME") || !defined('CLI_SCHEME')) {
    echo "CLI_SERVER_NAME or CLI_SCHEME is not configured. Check snapporder/config.php\n";
    die(1);
}
if(PHP_SAPI !== 'cli') {
    echo "Not allowed. Run this on the command line.\n";
    die(1);
}
if($argc < 2) {
    echo "Invalid number of arguments.\n";
    die(1);
}
if( !is_numeric($argv[1]) ) {
    echo "User ID is not a number.\n";
    die(1);
}

$user = get_user($argv[1]);
if( $user == NULL ) {
    echo "User with user ID $argv[1] not found.\n";
    die(1);
}

/* Add register url */
if($user['registration_status'] !== "partial") {
    echo "User ".$user['memberid']." has invalid registration status '".$user['registration_status']."' (not partial).\n";
    die(1);
}
$server_name = CLI_SERVER_NAME;
$scheme = CLI_SCHEME;
$user['registration_url'] = generate_registration_url($user, SECRET_KEY, $server_name, $scheme);

/* Send email */
send_activation_email(array(), $user);
echo "Email sent to ". $user['email'].".\n";
