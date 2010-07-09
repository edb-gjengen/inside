<?php

/**
 * Default command class is used if no command class is found
 */
class App_Command_Login extends App_Command_Command {
  
  function doExecute(App_Controller_Request $request) {
    $username = $request->getProperty("username");
    $password = $request->getProperty("password");
    if (empty($username) || empty($password)) {
      $request->addFeedback("Både brukernavn og passord må oppgis.");
      return self::statuses('CMD_INSUFFICIENT_DATA');
    }
    
    $manager = App_Base_ApplicationRegistry::getAccessManager();
    $user = $manager->login($username, $password);
    if (is_null($user)) {
      $request->addFeedback("Ugyldig brukernavn eller passord.");
      return self::statuses('CMD_ERROR');
    }
    App_Base_SessionRegistry::setUserId($user->getId());
    return self::statuses('CMD_OK');
  }
}

?>