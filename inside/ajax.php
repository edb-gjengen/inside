<?php
require_once "functions.php";
require_once "includes.php";
header("Content-type: application/xml");

print('<?xml version="1.0" encoding="ISO-8859-1"?'.'>');
   
$conn = db_connect();

$action = $_REQUEST['action'];
if ($action == 'checkZip'){

  $zip = $_REQUEST['zip']; 
  if ($zip != "") {  	
    $sql = "SELECT poststed AS postarea
           FROM din_postnummer
           WHERE postnummer = $zip";

  	$result = $conn->query($sql);
  	if ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
			$zip = $row->postarea;
  	}else {
  		$zip = "ugyldig postnummer";
  	}
  }else {
  	$zip = "ugyldig postnummer";
  }
?>
<postinfo>
  <postarea><?php print $zip; ?></postarea>
</postinfo>
<?php
}else if ($action == 'register-documentcategory'){
  $ap = new ActionParser();
  $ap->performAction();
  $cat = new DocumentCategory(scriptParam("documentcategoryid"));
  $type = "document";

  printAddCatXML($type, $cat);

}else if ($action == 'delete-documentcategory'){
  $ap = new ActionParser();
  $ap->performAction();
  $type = "document";
  printRemoveCatXML($type);
  
}else if ($action == 'register-eventcategory'){
  $ap = new ActionParser();
  $ap->performAction();
  $cat = new EventCategory(scriptParam("eventcategoryid"));
  $type = "event";

  printAddCatXML($type, $cat);

}else if ($action == 'delete-eventcategory'){
  $ap = new ActionParser();
  $ap->performAction();
  $type = "event";
  printRemoveCatXML($type);

}else if ($action == 'register-jobcategory'){
  $ap = new ActionParser();
  $ap->performAction();
  $cat = new JobCategory(scriptParam("jobcategoryid"));
  $type = "job";
  printAddCatXML($type, $cat);

}else if ($action == 'delete-jobcategory'){
  $ap = new ActionParser();
  $ap->performAction();
  $type = "job";
  printRemoveCatXML($type);

}else if ($action == 'checkUsername') {
  $username = $_REQUEST['username'];
  $conn = db_connect("forum");
  $sql = sprintf("SELECT user_id FROM phpbb_users u "."WHERE username_clean = '%s' ", strtolower($username));
  $result = & $conn->query($sql);
  if ($result->numRows() == 0) {
    $status = "true";
  }else {
  	$status = "false";
  }
?><username>
  <status><?php print $status; ?></status>
</username>
<?php
}


function printAddCatXML($type, $cat){
?>
<category>
  <actiontype>add</actiontype>
  <cat-type><?php print $type; ?></cat-type>
  <id><?php print $cat->id; ?></id>
  <title><?php print $cat->title; ?></title>
  <text><?php print $cat->text; ?></text>
  <edit-href><?php print getEditLink($cat->id, $type."category"); ?></edit-href>
  <edit-text><?php print getEditText($type."category"); ?></edit-text>
  <delete-href><?php print getDeleteLink($cat->id, $type."category"); ?></delete-href>
  <delete-text><?php print  getDeleteText($type."category"); ?></delete-text>
</category>
<?php
}

function printRemoveCatXML($type){
?>
<category>
  <actiontype>remove</actiontype>
  <cat-type><?php print $type; ?></cat-type>
  <id><?php print scriptParam($type."categoryid"); ?></id>
</category>
<?php

}
?>