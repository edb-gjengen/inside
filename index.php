<?php

$time_start = microtime(true);

require_once "./inside/includes.php";

$ap = new ActionParser();
$ap->performAction();

$p = new Page();
$p->display();

?>
