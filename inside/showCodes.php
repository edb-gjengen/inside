<?php
header("Content-type: text/plain");
for ($i = 1; $i <= 10000; $i++){
  print($i . "\t" . substr(crypt($i, 1813), 2, 6)."\n");
} 
?>
