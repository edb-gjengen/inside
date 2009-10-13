<?php

require_once "includes.php";

$id = $_GET["wid"];
$text = htmlentities($_GET["text"]);
$type = $_GET["type"];

$sql = "UPDATE weekprogram SET " .
         "  ".$type."text = '$text' " .
         "WHERE " .
         "  id = $id";
print($sql);
$conn = db_connect();
$result =& $conn->query($sql); 


?>