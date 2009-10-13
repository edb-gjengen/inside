<?php

class Concerts {
  var $conn;

  function Concerts(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }

    public function getList($start_date, $picture = false){
    if ($picture) {
    	$condition = "AND vedlegg != '0'";    
    }else {
    	$condition = "";
    }
    $sql = "SELECT id
            FROM program
            WHERE tid >= '$start_date' " .
           "$condition
            ORDER BY tid ASC";
    if ($this->conn == false){
      return false;
    }
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Concerts: " . $result->toString());
    }
  }

  public function getListWeek($start, $end){
    
    $sql = "SELECT id
            FROM program
            WHERE tid >= '$start' " .
           "AND tid <= '$end' 
            ORDER BY tid ASC";
    if ($this->conn == false){
      return false;
    }
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Concerts: " . $result->toString());
    }
  }

  public function getListMonth($year, $month){
    $nextMonth  = date("Y-m", strtotime("+1 month", strtotime("$year-$month-01")));
    $sql = "SELECT id, tittel, DAYOFMONTH(tid) AS date 
            FROM program
            WHERE tid >= '$year-$month-01'
            AND tid < '$nextMonth-01'
            ORDER BY tid ASC";
    if ($this->conn == false){
      return false;
    }
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Concerts: " . $result->toString());
    }
  }

  public function getListSelection($start, $end, $type){
    if ($type == "Billett") {
    	$condition = "AND billett != '' ";
    }else {
    	$condition = "AND type = '$type' ";    	
    }
    
    $sql = "SELECT id
            FROM program
            WHERE tid >= '$start' " .
           "AND tid <= '$end' " .
           "$condition " .
           "AND vedlegg != '0'
            ORDER BY tid ASC";
    if ($this->conn == false){
      return false;
    }
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Concerts: " . $result->toString());
    }
  }

  public 
  function displayList($selection = "0000-00-00"){
    $sql = "SELECT id AS concertId FROM program
            WHERE tid > $selection
            ORDER BY tid ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($result->numRows() > 0){
			print("<p>Antall treff: ".$result->numRows()."</p>");
?>
      <table class="sortable" id="concertlist">
        <tr>
          <th>tittel</th>
          <th>type</th>
          <th>sted</th>
          <th>arrangør</th>
          <th>dato</th>
          <?php if(checkAuth("view-edit-options-concert")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
            $concert = new Concert($row['concertId']);
            $concert->displayList();
          }
        print("      </table>");
      }else {
        print("<p>Ingen arrangementer er registrert.</p>");
      }
    }else {
      error("Concerts: " . $result->toString());
    }
    
  }

}


?>