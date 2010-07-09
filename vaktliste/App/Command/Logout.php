<?php

/**
 * Default command class is used if no command class is found
 */
class App_Command_Logout extends App_Command_Command {
  
  function doExecute(App_Controller_Request $request) {
    $request->addFeedback("Du er logget ut");
    App_Base_SessionRegistry::setUserId(null);
    return self::statuses('CMD_DEFAULT');
  }
}

?>