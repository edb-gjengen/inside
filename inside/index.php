<?php

$url = $_SERVER['SCRIPT_URI'];
if(substr($url, 0, 5) != 'https')
{
	$newurl = str_replace('http://','https://',$url);
	header("Location: $newurl");
	die;
}

$time_start = microtime(true);

require_once "includes.php";

$ap = new ActionParser();
$ap->performAction();

$p = new Page();
$p->display();

?>
