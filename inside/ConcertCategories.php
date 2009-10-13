<?php

class ConcertCategories {
  var $categories;

  function ConcertCategories(){
    $this->__construct();
  }

  function __construct(){
    if (isset($_SESSION['concert']['categories'])){
      $this->categories = $_SESSION['concert']['categories'];
    }else {
      $this->categories = getEnumValues("type", "program", "dns");
      $_SESSION['concert']['categories'] = $this->categories;
    }
  }

  public function getCategories(){
    return $this->categories;
  }

}
?>