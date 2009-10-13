<?php
print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');
require_once "includes.php";

header("Content-type: application/xml");

  
   
  $conn = db_connect();

  $posId = $_REQUEST['positionid']; 

  $sql = "SELECT name, text
          FROM din_position
          WHERE id = $posId";

  $result = $conn->query($sql);
  $row =& $result->fetchRow(DB_FETCHMODE_OBJECT);
?>

<position>
  <positionid><?php print $posId; ?></positionid>
  <title><?php print $row->name; ?></title>
  <text><![CDATA[<?php print $row->text; ?>]]></text>
</position>
