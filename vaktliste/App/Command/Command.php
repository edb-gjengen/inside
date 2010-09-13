<?php

abstract class App_Command_Command {
  
  private static $STATUS_STRINGS = array (
    'CMD_DEFAULT'           => 0,
    'CMD_OK'                => 1,
    'CMD_ERROR'             => 2,
    'CMD_INSUFFICIENT_DATA' => 3
    );
    
  private $status = 0;
  
  //private $currentUser = null;
  
  final function __construct() {}
  
  function execute(App_Controller_Request $request) {
    // check if visitor is logged in
    //$this->currentUser = App_Base_SessionRegistry::getCurrentUser();

    // execute command
    $this->status = $this->doExecute($request);
    $request->setCommand($this);
  }
  
  function getStatus() {
    return $this->status;
  }
  
  static function statuses ($str = 'CMD_DEFAULT') {
    if (empty($str)) $str = 'CMD_DEFAULT';
    // convert string into a status number
    return self::$STATUS_STRINGS[$str];
  }
  
  //abstract protected function validate(App_Controller_Request $request);
  abstract function doExecute(App_Controller_Request $request);
}

?>