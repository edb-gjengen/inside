<?php

class Events {
  var $conn;

  function Events(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }

  public function getListMonth($year, $month){
    $nextMonth  = date("Y-m", strtotime("+1 month", strtotime("$year-$month-01")));
    $sql = "SELECT id, name, DAYOFMONTH(time) AS date 
            FROM din_event
            WHERE time >= '$year-$month-01'
            AND time < '$nextMonth-01'
            ORDER BY time ASC";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Events: " . $result->toString());
    }
  }
  
  public function displayList($selection = "0000-00-00"){
    $sql = "SELECT id FROM din_event
            WHERE time > $selection
            ORDER BY time ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($result->numRows() > 0){
?>
      <table class="sortable" id="eventlist">
        <tr>
          <th>&nbsp;</th>
          <th>tittel</th>
          <th>type</th>
          <th>ansvarlig</th>
          <th>dato</th>
          <?php if(checkAuth("view-edit-options-event")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
            $event = new Event($row['id']);
            $event->displayList();
          }
        print("      </table>");
      }else {
        print("<p>Ingen aktiviteter registrert.</p>");
      }
    }else {
      error("Events: " . $result->toString());
    }
    
  }

}


?>