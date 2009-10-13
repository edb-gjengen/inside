<?php

require_once "includes.php";


$conn =& DB_connect();

$id = $_GET['id'];

$nodelist = array(); 

// Pull file meta-data   
$sql = "SELECT * FROM din_document WHERE id = $id";   
$result =& $conn->query($sql);
if (DB::isError($result) == true){
  print("Document: " . $result->toString());
}

if ($result->numRows() != 1) {     
  print("Not a valid file id!");   
} 

$FileObj = $result->fetchRow(DB_FETCHMODE_OBJECT); 
// Pull the list of file inodes   
$sql = "SELECT id FROM din_documentdata 
        WHERE document_id = $id 
        ORDER BY id"; 
$result =& $conn->query($sql);
if (DB::isError($result) == true){
  print("Document: " . $result->toString());
} 

while ($cur = $result->fetchRow(DB_FETCHMODE_OBJECT)) {     
  $nodelist[] = $cur->id;   
} 

// Send down the header to the client   
header("Pragma: public");
header("Cache-Control: cache, must-revalidate");   
header("Expires: 0");
header("Content-Type: $FileObj->type");   
header("Content-Length: " . $FileObj->size);
$FileObj->name = utf8_encode($FileObj->name);
header("Content-Disposition: attachment; filename=\"$FileObj->name\""); 
header("Content-Transfer-Encoding: binary");

// Loop thru and stream the nodes 1 by 1 
for ($i = 0 ; $i < count($nodelist) ; $i++) {     
  $sql = "SELECT data FROM din_documentdata WHERE id = $nodelist[$i]"; 
  $result =& $conn->query($sql);
  if (DB::isError($result) == true){
    print("Document: " . $result->toString());
  } else {
    $DataObj =& $result->fetchRow(DB_FETCHMODE_OBJECT);
    print $DataObj->data;   
  }
} 

?>