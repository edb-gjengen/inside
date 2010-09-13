<?php
header("Content-type: Text/plain");

require_once "includes.php";

$conn = & DB :: connect(getDSN());
if (DB :: isError($conn)) {
    print ("error: " . $conn->toString());
    exit ();
}

$i = 0;
$u = 0;

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