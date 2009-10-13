<?php

class OrderStatuses {
  var $id;
  var $value;
  var $conn;

  function OrderStatuses() {
    $this->__construct();
  }

  function __construct(){
    $this->conn =& db_connect();
  }

  public 
  function getList(){
  	$sql = "SELECT * FROM din_order_status";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
     	return $result;
    }else {
      error($result->toString());
    }
  }
}
?>
