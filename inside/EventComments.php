<?php

class EventComments {
  var $conn;

  function EventComments(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }

  public function getList($eventId){
    $sql = "SELECT id
            FROM din_eventcomment
            WHERE event_id = $eventId
            ORDER BY date ASC";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Eventcomments: " . $result->toString());
    }
  }  
}


?>