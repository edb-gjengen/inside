<?php

class App_Controller_ApplicationHelper {
  private static $instance;
  //private $config = "options.xml";
  
  private function __construct() {}
  
  static function instance() {
    if (!self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }
  
  function init() {
    $dsn = App_Base_ApplicationRegistry::getDSN();
    if (!is_null($dsn)) {
      return;
    }
    $this->getOptions();
    
    App_Base_SessionRegistry::instance();
  }
  
  private function getOptions() {
    // $this->ensure( file_exists( $this->config  ),  "Could not find options file" );
    // $options = @SimpleXml_load_file( $this->config );
    // $this->ensure( $options instanceof SimpleXMLElement, "Could not resolve options file" );
    // $dsn = (string)$options->dsn; 
    include("../inside/credentials.php");
    $dsn = getDSN("PDO");
    $this->ensure($dsn, "No DSN found");
    App_Base_ApplicationRegistry::setDSN($dsn);
    
    $map = new App_Controller_ControllerMap();
    //foreach ($options->control->view as $default_view) {
    //  $stat_str = trim($default_view['status']); 
    //  $status = App_Command_Command::statuses($stat_str);
    //  $map->addView('default', $status, (string)$default_view);
    //}
    //foreach ( $options->control->command as $command_view ) {
    //  $command =  trim((string)$command_view['name'] );
    //  if ( $command_view->classalias ) {
    //    $classroot = trim((string)$command_view->classalias['name']);
    //    $map->addClassroot( $command, $classroot  );
    //  }
    //  if ( $command_view->view ) {
    //    $view =  trim((string)$command_view->view);
    //    $forward = trim((string)$command_view->forward);
    //    $map->addView( $command, 0, $view );
    //    if ( $forward ) {
    //      $map->addForward( $command, 0, $forward );
    //    }
    //    foreach( $command_view->status as $command_view_status ) {
    //      $view =  trim((string)$command_view_status->view);
    //      $forward = trim((string)$command_view_status->forward);
    //      $stat_str = trim($command_view_status['value']); 
    //      $status = App_Command_Command::statuses( $stat_str );
    //      if ( $view ) {
    //        $map->addView( $command, $status, $view );
    //      }
    //      if ( $forward ) {
    //        $map->addForward( $command, $status, $forward );
    //      }
    //    }
    //  }
    //}
    
    // Default view
    $status = App_Command_Command::statuses();
    $map->addView("default", $status, "login");
    $status = App_Command_Command::statuses("CMD_OK");
    //$map->addView("default", $status, "");
    $status = App_Command_Command::statuses("CMD_ERROR");
    $map->addView("default", $status, "error");
    
    $map->addClassroot("login", "Login");
    $status = App_Command_Command::statuses();
    $map->addView("login", $status, "login");
    $status = App_Command_Command::statuses("CMD_OK");
    $map->addForward("login", $status, "view-calendar");
    
    $map->addClassroot("logout", "Logout");
    //$map->addClassroot("logout", "Logout");
    
    $map->addClassroot("add-shift", "AddShift");
    $status = App_Command_Command::statuses();
    $map->addView("add-shift", $status, "view-shift");
    $status = App_Command_Command::statuses("CMD_OK");
    $map->addForward("add-shift", $status, "view-calendar");
    
    $map->addClassroot("view-shift", "GetShift");
    $map->addView("view-shift", App_Command_Command::statuses(), "view-shift");
    
    $map->addClassroot("view-calendar", "ViewCalendar");
    $status = App_Command_Command::statuses();
    $map->addView("view-calendar", $status, "calendar");
    
    App_Base_ApplicationRegistry::setControllerMap($map);
    
    $appCtrl = new App_Controller_AppController(App_Base_ApplicationRegistry::getControllerMap());
    App_Base_ApplicationRegistry::setApplicationController($appCtrl);
    
    $manager = new App_Controller_AccessManager();
    App_Base_ApplicationRegistry::setAccessManager($manager);
    
    $pageTitle = "Det Norske Studentersamfund - vaktliste";
    App_Base_ApplicationRegistry::setPageTitle($pageTitle);
  
  }
  
  private function ensure ($expr, $message) {
    if (!$expr) {
      throw new Exception($message);
    }
  }
}

?>