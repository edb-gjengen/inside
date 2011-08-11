<?php
session_start();
require_once("../credentials.php");
$locale = Array('no_NO', 'nor_nor');
setlocale(LC_TIME, $locale);
//Extras
require_once("../functions.php");
require_once("../language.php");
set_include_path("../../includes");
$include_path = "../../includes/";
//PEAR-class
require_once($include_path."DB.php");
require_once("../User.php");

function update_user($username, $password) {
    $conn = db_connect();
    $uid = getCurrentUser();

    /* Update the current user's username and password */
    $sql = sprintf("UPDATE din_user SET username='%s', password=PASSWORD('%s') WHERE id=%s LIMIT 1",
                mysql_real_escape_string($username),
                mysql_real_escape_string($password),
                $uid);
    $result = $conn->query($sql);
    return ! DB :: isError($result);
}
function user_exists($username) {
    $conn = db_connect();

    $sql = sprintf("SELECT id FROM din_user WHERE username = '%s'", mysql_real_escape_string($username));
    $result = $conn->query($sql);
    $userExists = $result->numRows() > 0;

    return $userExists;
}
/* Update the migration status */
function set_migrated() {
    $conn = db_connect();
    $uid = getCurrentUser();

    $sql = sprintf("UPDATE din_user SET migrated=NOW() WHERE id=%s", $uid);
    $result = $conn->query($sql);

    if (DB :: isError($result)) {
        return false;
    }
}
/* Note: copied from functions.php */
if( !function_exists('is_migrated') ) {
    function is_migrated() {
        $conn = db_connect();
        $uid = getCurrentUser();

        $sql = sprintf("SELECT migrated FROM din_user WHERE id=%s AND migrated IS NOT NULL", $uid);
        $result = $conn->query($sql);

        if (DB :: isError($result) == true) {
            error("is_migrated: " . $result->toString());
            return false;
        }
        return $result->numRows() > 0;
    }
}
function find_groups() {
    $conn = db_connect();
    $uid = getCurrentUser();
    $sql = "SELECT g.posix_group
        FROM din_usergrouprelationship ugr, din_user u, din_group g
        WHERE ugr.user_id = $uid
        AND ugr.user_id = u.id
        AND ugr.group_id = g.id";
    $conn->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $conn->getAll($sql);

    if(DB :: isError($result)) {
        return false;
    }
    $arr = array();
    foreach($result as $column => $group) {
        $arr[] = $group['posix_group'];
    }

    return $arr;
}
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
