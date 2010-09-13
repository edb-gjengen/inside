<?php

class App_Controller_AppController {
  private static $base_cmd;
  private static $default_cmd;
  private $controllerMap;
  private $invoked = array();
  
  function __construct(App_Controller_ControllerMap $map) {
    $this->controllerMap = $map;
    if (!self::$base_cmd) {
      self::$base_cmd = new ReflectionClass("App_Command_Command");
      self::$default_cmd = new App_Command_DefaultCommand();
    }
  }
  
  function getView(App_Controller_Request $request) {
    $view = $this->getResource($request, "View");
    return $view;
  }
  
  function getForward(App_Controller_Request $request) {
    $forward = $this->getResource($request, "Forward");
    if ($forward) {
      $request->setProperty("cmd", $forward);
    }
    return $forward;
  }
  
  private function getResource(App_Controller_Request $request, $res) {
    // get the previous command and its execution status
    $cmd_str = $request->getProperty("cmd");
    $previous = $request->getLastCommand();
    $status = $previous->getStatus();
    if (!$status) {
      $status = 0;
    }
    
    $acquire = "get$res";
    // find resource for previous command and its status
    $resource = $this->controllerMap->$acquire($cmd_str, $status);
    // alternatively find resource for command and status 0
    if (!$resource) {
      $resource = $this->controllerMap->$acquire($cmd_str, 0);
    }
    // or command 'default' and command status
    if (!$resource) {
      $resource = $this->controllerMap->$acquire('default', $status);
    }
    // all else has failed get resource for 'default', status 0
    if (!$resource) {
      $resource = $this->controllerMap->$acquire('default', 0);
    }
    return $resource;
  }
  
  function getCommand(App_Controller_Request $request) {
    $previous = $request->getLastCommand();
    if (!$previous) {
      // this is first command for this request
      $cmd = $request->getProperty("cmd");
      if (!$cmd) {
        // no command property - using default
        $request->setProperty("cmd", "default");
        return self::$default_cmd;
      }
    } else {
      // a command has been run already in this request
      $cmd = $this->getForward($request);
      if (!$cmd) {
        return null;
      }
    }
    
    // we now have a command name in $cmd - turn it into a Command object
    $cmd_obj = $this->resolveCommand($cmd);
    if (!$cmd_obj) {
      throw new Exception("Couldn't resolve '$cmd'");
    }
    
    // Count the number of times the class has been accessed and check for circular forwarding
    $cmd_class = get_class($cmd_obj);
    if (isset($this->invoked[$cmd_class])) {
      $this->invoked[$cmd_class]++;
    } else {
      $this->invoked[$cmd_class] = 1;
    }
    
    if ($this->invoked[$cmd_class] > 1) {
      throw new Exception("Circular forwarding");
    }
    
    // return the Command object
    return $cmd_obj;
  }
  
  function resolveCommand($cmd) {
    $sep = "/";
    $cmd = str_replace(array(".", $sep), "", $cmd);
    $classroot = $this->controllerMap->getClassroot($cmd);
    $filepath = "App{$sep}Command{$sep}{$classroot}.php";
    $classname = "App_Command_{$classroot}";
    if (file_exists($filepath)) {
      @require_once("$filepath");
      if (class_exists($classname)) {
        $cmd_class = new ReflectionClass($classname);
        if ($cmd_class->isSubClassOf( self::$base_cmd )) {
          return $cmd_class->newInstance();
        }
      }
    }
    return null;
  }
}

?>