<?php

$time_start = microtime(true);

require_once "includes.php";

$ap = new ActionParser();
$ap->performAction();

$p = new Page();
$p->display();

?>