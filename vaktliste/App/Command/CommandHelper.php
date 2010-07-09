<?php

class App_Command_CommandHelper {
  static function getCurrentUser() {
    if (!is_null(App_Base_SessionRegistry::getUserId())) {
      return App_Domain_DomainObject::getFinder("App_Domain_User")->find(App_Base_SessionRegistry::getUserId());
    }
    return null;
  }
}

?>
