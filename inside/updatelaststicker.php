<?php
header("Content-type: application/xml");
print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');

require_once "includes.php";

$userid = $_GET['userid'];
$value = $_GET['value'];
$user = new User($userid);
$user->updateLastSticker($value);

?>
