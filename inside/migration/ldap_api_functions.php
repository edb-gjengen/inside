<?php
require_once("inside_functions.php");
/* This file is an example on how you might push users from inside to ldap/radius.
 *  * Basically, fill an array with the required attributes, get the API_KEY and the encryption key
 *   * and send it to the file addNewUser.php under brukerinfo.neuf.no.
 *    */

function ldap_add_user($username, $firstname, $lastname, $email, $password, $groups) {
    /* REQUIRED FOR THE SERVER-SIDE */
    $API_KEY = $_SERVER['API_KEY1'];
    $ENC_KEY = $_SERVER['ENC_KEY'];
    /* END REQUIRED */

    $b64_enc_password = enc_password($password, $ENC_KEY);

    /* User data */
    $user = array();
    $user['username'] = $username;
    $user['firstname'] = $firstname;
    $user['lastname'] = $lastname;
    $user['email'] = $email;
    $user['password'] = $b64_enc_password;
    $user['groups'] = $groups;

    $user['api_key'] = $API_KEY;

    /* Build URL for POSIX groups. */
    $arr = array();
    foreach( $user as $key => $value ) {
        if( $key == "groups" ) {
            array_push($arr, $key . "=" . urlencode(implode(",", $value)) );
            continue;
        }
        /* Add URL-encoded value to array */
        array_push($arr, $key . "=" . urlencode($value));
    }

    /* Send the HTTP request. */
    $result = var_export(file_get_contents("http://brukerinfo.neuf.no/addNewUser.php?" . implode("&", $arr)), true);
    return $result;
}
function _log($str) { 
    $time = date("[Y-m-d H:i:s.u]"); 
    file_put_contents("migration.log", $time . 
    $str . "\n", FILE_APPEND); 
} 

?>
