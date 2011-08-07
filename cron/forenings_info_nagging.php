<?php

require 'db.php';


$res = mysql_query("SELECT * FROM `din_division` WHERE `updated` IS NULL or `updated` + INTERVAL 90 DAY < now()");

while($row = mysql_fetch_assoc($res))
{
	$time = strtotime($row['updated']);
	$diff = $time - time();
	$diff /= 60 * 60 * 24;
	$diff = round($diff);
	var_dump($row);
	if($diff == 0)
	{

	}
	else if($diff == 7)
	{
	
	}
	else if($diff == 14)
	{

	}
	else if($diff == 21)
	{

	}
}
