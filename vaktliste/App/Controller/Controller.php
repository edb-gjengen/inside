<?php

class App_Controller_Controller {
  private $applicationHelper;
  
  private function __construct() {}
  
  public static function run() {
    $instance = new self();
    $instance->init();
    $instance->handleRequest();
  }
  
  public function init() {
    $this->applicationHelper = App_Controller_ApplicationHelper::instance();
    $this->applicationHelper->init();
  }
  
  public function handleRequest() {
    $request = new App_Controller_Request();
    $app_c = App_Base_ApplicationRegistry::getApplicationController();
    while ($cmd = $app_c->getCommand($request)) {
      $cmd->execute($request);
    }
    $this->invokeView($app_c->getView($request));
  }
  
  function invokeView($target) {
    include("App/View/$target.php");
    exit;
  }
}

?>