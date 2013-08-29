<?php

$messages = Array();
$msg_counter = 0;

class Messages {

  public function addMSG($msg){
    if ($GLOBALS['beginHTML'] == true){
      Messages::display($msg);
    }else {
      $GLOBALS['messages'][$GLOBALS['msg_counter']++] = "$msg";
    }
  }

  public function display($msg = NULL){?>
    <div class="messages fancybox alert alert-warning">
  
      <ul>
<?php    
   $GLOBALS['beginHTML'] = true;
   if ($msg != NULL){
       print("<li>$msg</li>\n");
   }else {
     foreach ($GLOBALS['messages'] as $e){
       print("<li>$e</li>\n");
     }
   }?>
      </ul>
    </div>
<?php
  }

}

?>
