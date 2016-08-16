<?php
header("Content-type: Text/plain");

require_once "includes.php";

$conn = db_connect();
if (DB :: isError($conn)) {
    print ("error: " . $conn->toString());
    exit();
}

$i = 0;
$u = 0;

$user = new User(735);

$card = $user->getMembershipCard();

//print_r($card);

if (is_null($card)) {
  print "User is missing card!\n\n";
  print "Creating card ..\n";
  $newcard = new MembershipCard();
  $newcard->setOrdered(new DateTime());
  $user->setMembershipCard($newcard);
  $card = $user->getMembershipCard();
  $card->store();
}

print "User " . $user->getName() . " has the following card: \n";
print "Card id " . $card->getId() . "\n";
if (!is_null($card->getOrdered())) {
  print "Ordered " . $card->getOrdered()->format(DateTime::RFC1123) . "\n";
}
if (!is_null($card->getProduced())) {
  print "Produced " . $card->getProduced()->format(DateTime::RFC1123) . "\n";
}
if (!is_null($card->getDelivered())) {
  print "Delivered " . $card->getDelivered()->format(DateTime::RFC1123) . "\n";
}
print "Active " . $card->getActive() . "\n";

$produced = new DateTime();
$produced->modify("+1 month");
$card->setProduced($produced);

print "New time: " . $card->getProduced()->format('U') . "\n";

$card->setActive(false);

$card->store();

print_r($card);

return true;

$sql = "SELECT u.id, u.cardno, u.hasCard, uc.date, u.expires FROM din_user u LEFT JOIN din_usedcardno uc ON u.cardno = uc.id ORDER BY u.cardno";
$result = & $conn->query($sql);
if (DB :: isError($result) != true) {
  while($row = & $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    if ($row["cardno"] > 0) {
      $ins_stmt = "INSERT INTO din_membershipcard (id, userId, ordered, produced, delivered, active) VALUES ("; 
      $ins_stmt .= $row["cardno"] . ", ";
      $ins_stmt .= $row["id"] . ", ";
      if ($row["date"]) {
        $ins_stmt .= "\"" . $row["date"] . "\", ";
      } else {
        $ins_stmt .= "\"2005-12-28 16:28:31\", ";
      }
      
      if ($row["hasCard"] == 1) {
        if ($row["date"]) {
          $ins_stmt .= "\"" . $row["date"] . "\", ";
        } else {
          $ins_stmt .= "\"2005-12-28 16:28:31\", ";
        }
      } else {
        $ins_stmt .= "NULL, ";
      }
      
      if ($row["hasCard"] == 1) {
        if ($row["date"]) {
          $ins_stmt .= "\"" . $row["date"] . "\", ";
        } else {
          $ins_stmt .= "\"2005-12-28 16:28:31\", ";
        }
      } else {
        $ins_stmt .= "NULL, ";
      }
      $ins_stmt .= $row["hasCard"];
      $ins_stmt .=  ")";
      
      //print $ins_stmt . "\n";
      //$conn->query($ins_stmt);
      $i++;
    }
  }
}

print "\n\nInsert $i rows\nUpdated $u rows";
