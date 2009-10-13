<?php

$errors = Array();
$err_counter = 0;

class Errors {
  var $displayed;

  public function addError($msg){
    if ($GLOBALS['beginHTML'] == true){
      Errors::display($msg);
    }else {
      $GLOBALS['errors'][$GLOBALS['err_counter']++] = "$msg";
    }
  }
  
  public function display($msg = NULL){
    if ($_SESSION['show-errors'] == true || isAdmin()){
?>
    <div class="errors">
      <h3>Feilmeldinger</h3>
      <ul>
<?php 
   $GLOBALS['beginHTML'] = true;
   if ($msg != NULL){
     print("<li>$msg</li>\n");
   }else {
     foreach ($GLOBALS['errors'] as $e){
       print("<li>$e</li>\n");
     }
   }?>
      </ul>
    </div>
<?php
    }
  }
}

?>