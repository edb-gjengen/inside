<?php
require_once('config.php');
/* This file is an example on how you might push users from inside to ldap/radius.
 *  * Basically, fill an array with the required attributes, get the API_KEY and the encryption key
 *   * and send it to the HTTP endpoint SYNC_NEW_USER_URL.
 *    */

function enc_password($password, $key) {
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $password, MCRYPT_MODE_ECB, $iv);

    $b64enc = base64_encode($crypttext);
    return $b64enc;
    /* other end */
    //$key = "This is a very secret key";
    //$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    //$b64dec = base64_decode($b64enc);
    //$cleartext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $b64dec, MCRYPT_MODE_ECB, $iv);
}

function ldap_add_user($username, $firstname, $lastname, $email, $password, $groups) {
    /* REQUIRED FOR THE SERVER-SIDE */
    $ENC_KEY = ENC_KEY;
    $API_KEY = API_KEY;
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
    $result = var_export(file_get_contents(SYNC_NEW_USER_URL."?" . implode("&", $arr)), true);
    return $result;
}
function _log($str) { 
    $time = date("[Y-m-d H:i:s.u]"); 
    // relative to the file _log is in
    file_put_contents(dirname(__FILE__)."/migration.log", $time.$str."\n", FILE_APPEND); 
} 


?>
