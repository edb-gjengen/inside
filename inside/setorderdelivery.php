<?php
header("Content-type: application/xml");
print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');

require_once "includes.php";

if(!isAdmin()) {
	die("N/A");
}

$orderid = mysql_escape_string($_GET["orderid"]);
$newstatusid = mysql_escape_string($_GET["newstatusid"]);

//$user = new User($userid);
$conn = db_connect();

$sql = "UPDATE din_order SET order_deliverystatus_id='$newstatusid' WHERE id='$orderid'";
$result =& $conn->query($sql);

if (DB::isError($result) == true){
//	notify('Fikk ikke oppdatert status.');
//	error('List buyers: ' . $result->toString());
}
?>
<user>
  <orderno><?php print $orderid; ?></orderno>
  <deliverystatus><?php print $newstatusid; ?></deliverystatus>
</user>
