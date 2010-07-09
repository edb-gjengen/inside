<?php
//require_once( "App/Base/Registry.php" );

class App_View_ViewHelper {
  static function getRequest() {
    return App_Base_RequestRegistry::getRequest();
  }
}

?>
