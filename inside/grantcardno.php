<?php
header("Content-type: application/xml");
print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');

require_once "includes.php";

$userid = $_GET["userid"];

$user = new User($userid);
$user->grantCardno();

?>
<user>
  <cardno><?php print $user->cardno; ?></cardno>
  <expires><?php print date("Y", strtotime($user->expires)); ?></expires>
</user>
