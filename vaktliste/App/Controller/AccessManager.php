<?php

/**
 * Manage user access to commands and handle login 
 */
class App_Controller_AccessManager {
  private $error;
  
  function getError() {
    return $this->error;
  }
  
  function setError($msg) {
    $this->error = $msg;
  }
  
  function login($username, $password) {
    return App_Domain_DomainObject::getFinder(App_Domain_HelperFactory::USER)->findByLogin($username, $password);
  }
  
  function logout() {
    if (!is_null(App_Base_SessionRegistry::getUserId())) {
      // do something, i.e. logging ?
    }
    return true;
  }
  
  function getCommandAccess($cmd) {
    if (!is_null(App_Base_SessionRegistry::getUserId())) {
      return true;
    }
    return false;
  }
  
}