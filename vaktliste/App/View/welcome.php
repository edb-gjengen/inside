<?php 

$request = App_View_ViewHelper::getRequest();
$currentUser = $request->getObject('currentUser');
//$venue = $request->getObject('venue');

include("Common/header.php");
?>

<div>
  <h1>Velkommen til vaktlisten!</h1>
<?php 
  print "<p>Du er logget inn som " . $currentUser->getName() . ".</p>\n";
  print $request->getFeedbackString("</td></tr><tr><td>");
?>

</div>

<?php 
include("Common/footer.php");
?>