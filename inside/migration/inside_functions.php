<?php
session_start();
require_once("../credentials.php");
$locale = Array('no_NO', 'nor_nor');
setlocale(LC_TIME, $locale);
//Extras
require_once("../functions.php");
require_once("../language.php");
set_include_path(get_include_path() . PATH_SEPARATOR . "../../includes");
require_once("DB.php"); // PEAR
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
/* 
 * Update the migration status.
 * Note: copied from functions.php
 */
if( !function_exists('set_migrated') ) {
function set_migrated($uid) {
    $conn = db_connect();

    $sql = sprintf("UPDATE din_user SET migrated=NOW() WHERE id=%s", $uid);
    $result = $conn->query($sql);

    if (DB :: isError($result)) {
        return false;
    }
}
}
/* Note: copied from functions.php */
if( !function_exists('is_migrated') ) {
    function is_migrated($uid) {
        $conn = db_connect();

        $sql = sprintf("SELECT migrated FROM din_user WHERE id=%s AND migrated IS NOT NULL", $uid);
        $result = $conn->query($sql);

        if (DB :: isError($result) == true) {
            error("is_migrated: " . $result->toString());
            return false;
        }
        return $result->numRows() > 0;
    }
}
/* Note: copied from functions.php */
if( !function_exists('membership_expired') ) {
    function membership_expired($uid) {
        $conn = db_connect();

        $sql = sprintf("SELECT expires FROM din_user WHERE id=%s AND expires <= NOW()", $uid);
        $result = $conn->query($sql);

        if (DB :: isError($result) == true) {
            error("membership_expired: " . $result->toString());
            return false;
        }
        return $result->numRows() > 0;
    }
}

/* Note: copied from functions.php */
if( !function_exists('find_groups') ) {
    function find_groups($uid) {
        $conn = db_connect();
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
}
