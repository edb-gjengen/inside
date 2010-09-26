<?php

if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')
{
        header("Location: https://www.studentersamfundet.no{$_SERVER['REQUEST_URI']}");
        die;
}

$time_start = microtime(true);

require_once "includes.php";

$ap = new ActionParser();
$ap->performAction();

$p = new Page();
$p->display();

?>
