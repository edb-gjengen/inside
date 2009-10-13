<?php

class BarShifts {
  var $conn;

  function BarShifts(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }

  public function getListMonth($year, $month){
    $nextMonth  = date("Y-m", strtotime("+1 month", strtotime("$year-$month-01")));
    $sql = "SELECT id, title, DAYOFMONTH(date) AS date, num_workers 
            FROM din_barshift
            WHERE date >= '$year-$month-01'
            AND date < '$nextMonth-01'
            ORDER BY date, start ASC";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("BarShifts: " . $result->toString());
    }
  }
  
  public function displayList($selection = "0000-00-00"){
    $sql = "SELECT id FROM din_barshift
            WHERE date >= $selection
            ORDER BY date, start ASC";
 	
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($result->numRows() > 0){
?>
  		<p>Antall vakter: <?php print $result->numRows(); ?></p>
      <table class="sortable" id="barshiftlist">
        <tr>
          <th>tittel</th>
          <th>sted</th>
          <th>dato</th>
          <th>start</th>
          <th>slutt</th>
          <th>status</th>
          <?php if(checkAuth("view-edit-options-barshift")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
            $barshift = new BarShift($row['id']);
            $barshift->displayList();
          }
        print("      </table>");
      }else {
        print("<p>Ingen vakter registrert.</p>");
      }
    }else {
      error("BarShifts: " . $result->toString());
    }    
  }
}
?>