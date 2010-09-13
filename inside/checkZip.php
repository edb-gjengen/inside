<?php
require_once "functions.php";
require_once "includes.php";
header("Content-type: application/xml");

  print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');
   
  $conn = db_connect();

  $zip = $_REQUEST['zip']; 

  $sql = "SELECT poststed AS postarea
          FROM din_postnummer
          WHERE postnummer = $zip";

  $result = $conn->query($sql);
  if ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
?><postinfo>
  <postarea><?php print $row->postarea; ?></postarea>
</postinfo>
<?php
}else {
?><postinfo>
  <postarea>ugyldig postnummer</postarea>
</postinfo>
<?php  
}
?>
