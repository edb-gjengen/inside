<?php

define("ARTIST", "1");
define("SHOW", "2");
  
class Categories {
  var $categories;
  var $conn;

  function Categories(){
    Categories::__construct();
  }

  function __construct(){
    $conn =& DB::connect(getDSN());
    if (DB::isError($conn)){
      error("Categories: " . $conn->toString());
      exit();
    }else {
      $this->conn = $conn;
    }

    $sql = "SELECT id, title FROM tf_article_category";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->categories = $result;
    }
  }

  public function getCategories(){
    return $this->categories;
  }

}
?>