<?php
// Retrieve the data source name for connecting to the MySQL server and
// selecting the sampdb database using our top-secret name and password

// To use the original MySQL module, $driver should be "mysql".
// To use the "MySQL improved" module, $driver should be "mysqli".
// This function is bloated to be able to use the same version on different servers.
function getDSN($host = "default") {
	if ($host == "default" || $host == "dns") {
		$host_name = "localhost";
		$driver = "mysql";
		$user_name = "USERNAME";
		$password = "PASSWORD";
		$db_name = "DATABASE";
	}
	else
		if ($host == "forum") {
			$host_name = "localhost";
			$driver = "mysql";
			$user_name = "USERNAME";
			$password = "PASSWORD";
			$db_name = "DATABASE";
		}

	return "$driver://$user_name:$password@$host_name/$db_name";
}
?>
