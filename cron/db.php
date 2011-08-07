<?php

require '../inside/credentials-sample.php';

$str = getDSN();

preg_match('|([^:]+)://([^:]+):([^@]+)@([^/]+)/(.+)|', $str, $output);

list($str, $driver, $user_name, $password, $host_name, $db_name) = $output;

if(!mysql_connect($host_name, $user_name, $password)) die("error connecting to db");
if(!mysql_select_db($db_name)) die('error selecting databse');
