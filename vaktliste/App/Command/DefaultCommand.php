<?php

/**
 * Default command class is used if no command class is found
 */
class App_Command_DefaultCommand extends App_Command_Command {

  //protected function validate(App_Controller_Request $request) {
  //}
  
  function doExecute(App_Controller_Request $request) {
    // check if user is logged in
    $user = App_Command_CommandHelper::getCurrentUser();
    if (!is_null($user)) {
      $request->setObject('currentUser', $user);
      return self::statuses('CMD_OK');
    }
//    if ($request->getProperty("p") == "login") {
//      $request->addFeedback( "you are logged in" );
//      return self::statuses('CMD_OK');
//    }
    return self::statuses('CMD_DEFAULT');
  }
}
