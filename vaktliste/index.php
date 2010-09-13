<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  
  set_include_path(get_include_path() . PATH_SEPARATOR . "./");
  //require("App/Support/autoloader.php");
  
  require("App/Base/Registry.php");
  require("App/Base/ApplicationRegistry.php");
  require("App/Base/RequestRegistry.php");
  require("App/Base/SessionRegistry.php");
  require("App/Command/Command.php");
  require("App/Command/CommandHelper.php");
  require("App/Command/DefaultCommand.php");
  require("App/Controller/AccessManager.php");
  require("App/Controller/AppController.php");
  require("App/Controller/ApplicationHelper.php");
  require("App/Controller/Controller.php");
  require("App/Controller/ControllerMap.php");
  require("App/Controller/Request.php");
  require("App/Domain/DomainObject.php");
  require("App/Domain/HelperFactory.php");
  require("App/Domain/ObjectWatcher.php");
  require("App/Mapper/Collection.php");
  require("App/Mapper/DomainObjectFactory.php");
  require("App/Mapper/Mapper.php");
  require("App/View/ViewHelper.php");
  
  require("App/Calendar/Calendar.php");
  
  require("App/Domain/Division.php");
  require("App/Mapper/DivisionCollection.php");
  require("App/Mapper/DivisionMapper.php");
  
  require("App/Domain/Group.php");
  require("App/Mapper/GroupCollection.php");
  require("App/Mapper/GroupMapper.php");
  
  require("App/Domain/Location.php");
  require("App/Mapper/LocationCollection.php");
  require("App/Mapper/LocationMapper.php");
  
  require("App/Domain/WorkShift.php");
  require("App/Mapper/WorkShiftCollection.php");
  require("App/Mapper/WorkShiftMapper.php");
  
  require("App/Domain/WorkSalaryType.php");
  require("App/Mapper/WorkSalaryTypeCollection.php");
  require("App/Mapper/WorkSalaryTypeMapper.php");
  
  require("App/Domain/User.php");
  require("App/Mapper/UserCollection.php");
  require("App/Mapper/UserMapper.php");
  require("App/Mapper/UserObjectFactory.php");
  
  //$app = new App_Controller_Controller();
  App_Controller_Controller::run();

?>