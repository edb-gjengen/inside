<?php
header("Content-type: application/xml");
print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');

require_once "includes.php";

$userid = $_GET["userid"];

$user = new User($userid);
$user->setCardProduced(1);
$user->updateLastSticker($user->getExpiryDate());
$user->sendCardProducedNotifyMail();

?>
<user>
  <cardno><?php print $user->cardno; ?></cardno>
  <laststicker><?php print $user->lastSticker; ?></laststicker>
</user>
