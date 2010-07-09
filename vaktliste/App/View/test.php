<html>
<head>
<title>Apptest</title>
</head>
<body>
<h1>Welcome</h1>

<?php 
foreach (App_Base_RequestRegistry::getRequest()->getFeedback() as $feedback) {
  print $feedback . "<br>";
}
?>
</body>
</html>