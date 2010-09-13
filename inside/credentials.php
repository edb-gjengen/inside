<?php
// Retrieve the data source name for connecting to the MySQL server and
// selecting the sampdb database using our top-secret name and password

// To use the original MySQL module, $driver should be "mysql".
// To use the "MySQL improved" module, $driver should be "mysqli".
// This function is bloated to be able to use the same version on different servers.
function getDSN($host = "default") {
	if ($host == "default" || $host == "dns" || $host == "PDO") {
		$host_name = "localhost";
		$driver = "mysql";
		$user_name = "glium337_dbuser";
		$password = "sdfXhu4Shgfd";
		$db_name = "glium337_dns";

	  if ($host == "PDO") {
	    // return PDO compatible DSN
	    return array(
	      "dsn"      => $driver .":dbname=".$db_name.";host=".$host_name,
	      "username" => $user_name,
	      "password" => $password
	      );
	  }
	}	elseif ($host == "forum") {
			/*$host_name = "localhost";
			$driver = "mysql";
			$user_name = "forum2";
			$password = "e9bAlSu1oI";
			$db_name = "phpbb1";*/
			$host_name = "localhost";
			$driver = "mysql";
			$user_name = "glium337_phpbb";
			$password = "n0ffn0ff";
			$db_name = "glium337_phpbb";
	}

	return "$driver://$user_name:$password@$host_name/$db_name";
}
?>