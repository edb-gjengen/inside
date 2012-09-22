<?php

define("LDAP_ENABLED", true);
define("LDAP_SERVER", "pacman.neuf.no");

define("KSUFFIX", "cn=krbcontainer");
define("ASUFFIX", "ou=Automount");
define("USUFFIX", "ou=People");
define("GSUFFIX", "ou=Groups");
define("SUFFIX", "dc=neuf,dc=no");

define("KERBEROS_DOMAIN", "NEUF.NO");

define("LOGIN_SHELL", "/bin/bash");
define("SHADOW_EXPIRE", "-1");
define("SHADOW_FLAG", "0");
define("SHADOW_WARNING", "7");
define("SHADOW_MIN", "8");
define("SHADOW_MAX", "999999");
define("SHADOW_LAST_CHANGE", "10877");

define("HOME_DIRECTORY_PREFIX", "/home");

define("BIND_USER", apache_getenv("BIND_USER"));
define("BIND_PASS", apache_getenv("BIND_PASS"));

define("UID_MIN", "10000");
define("UID_MAX", "29999");

define("GID_MIN", "9000");
define("GID_MAX", "9999");

define("USER_GID_MIN", "10000");
define("USER_GID_MAX", "29999");

define("USERNAME_REGEXP", "/^[a-z]{3,15}$/");

define("FILESERVER", "wii.neuf.no");
define("FILESERVER_HOMES", "/fileserver/homes");

define("RADIUS_MYSQL_HOST", "snes.neuf.no");
define("RADIUS_MYSQL_USER", apache_getenv("RADIUS_MYSQL_USER"));
define("RADIUS_MYSQL_PASS", apache_getenv("RADIUS_MYSQL_PASS"));

$api_keys = array();
$api_keys[] = apache_getenv("API_KEY1");


function __autoload($class_name) {
	
	require_once($class_name . ".class.php");
	
}

function lmPassword($password) {
	$hash = new SmbHash();
	return $hash->lmhash($password);
}

function ntPassword($password) {
	$hash = new SmbHash();
	return $hash->nthash($password);
}

function ssha($password) {
	return ssha_encode($password);
/*	$salt = sha1(rand());
	$salt = substr($salt, 0, 4);
	$hash = base64_encode( sha1 ($password . $salt, true) . $salt );
	return $hash;*/
}

function ssha_encode($password) {
	$salt = "";
	for ( $i=1;$i<=10;$i++ ) {
		$salt .= substr('0123456789abcdef', rand(0,15), 1);
	}
	$hash = base64_encode(pack("H*", sha1($password.$salt)).$salt);
	
	return $hash;
}

function debug($str) {
	$time = date("[Y-m-d H:i:s] ");
	echo $time . $str . "\n";
}

function ldap_log($str) {
	$time = date("[Y-m-d H:i:s] ");
	//file_put_contents("/var/www/neuf.no/userinfo/log/user.log", $time .
	//	$str . "\n", FILE_APPEND);
}

function _log_check($str) {
	$time = date("[Y-m-d H:i:s] ");
//	file_put_contents("/var/www/neuf.no/userinfo/log/integrity_check.log", $time .
//		$str . "\n", FILE_APPEND);
	debug($str);
}

function _log_check_error($str) {
	$time = date("[Y-m-d H:i:s] ");
//	file_put_contents("/var/www/neuf.no/userinfo/log/integrity_check.log", $time . "[ERROR] " .
//		$str . "\n", FILE_APPEND);
	debug($str);

}

function mail_admins($str) {
	_log_check("Mailing admins...");
}

function validate_user_info($user) {
	if( !is_array($user) ) {
		throw new Exception("ERROR: user array is not an array");
	}

	if( preg_match(USERNAME_REGEXP, $user['username']) == 0 ) {
		throw new Exception("ERROR: username did not validate");
	}

	if( empty($user['firstname']) ) {
		throw new Exception("ERROR: firstname missing");
	}

	if( empty($user['lastname']) ) {
		throw new Exception("ERROR: lastname missing");
	}

	if( empty($user['email']) ) {
		throw new Exception("ERROR: email missing");
	}
	
	if( empty($user['password']) ) {
		throw new Exception("ERROR: password missing");
	}
}
