<?php

class WeekPrograms {
  var $conn;

  function WeekPrograms($limit = 5) {
    $this->__construct($limit);
  }

  function __construct($limit = 5){
    $this->conn = db_connect();
  }

  public function getList($division = NULL){
    if ($division != NULL){
       $condition = "AND p.division_id = $division"; 
    }else {
       $condition = ""; 
    }
    $sql = "SELECT d.id AS divId, d.name AS divName, p.id AS positionId, p.name AS weekprogramName " .
           "FROM weekprogram p, din_division d " .
           "WHERE p.division_id = d.id " .
           "$condition" .
           "ORDER BY d.name ASC";

    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    } else {
      error("WeekPrograms: " . $result->toString());
      return false;
    }
  }
  
  public function getWeekProgramsAsArray(){
    if ($list = $this->getWeekPrograms()){
      $pos = Array();
      while ($row =& $list->fetchRow(DB_FETCHMODE_ASSOC)){
        $div = $row['divId'];
        
        $pos[] = Array("id" => $row['weekprogramId'],
                       "title" => $row['weekprogramName'],
                       "parent" => $row['weekprogramDivId']);
      }
      return $pos;
    }
  }



  public function display(){
    $sql = "SELECT id FROM weekprogram
            ORDER BY year DESC, week DESC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        $weekprogram = new WeekProgram($row->id);
        $weekprogram->display();
      }
    }else {
      error("WeekPrograms: " . $result->toString());
    }
    
  }

  public function displayList(){
    $sql = "SELECT id FROM weekprogram " .
           "ORDER BY year DESC, week DESC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if($result->numRows() > 0){
?>
      <table class="sortable" id="weekprogramlist">
        <tr>
          <th>uke</th>
          <th>fra</th>
          <th>til</th>
          <?php if(checkAuth("view-options-weekprogram")){
        ?><th colspan="5">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
            $weekprogram = new WeekProgram($row->id);
            $weekprogram->displayList();
          }
        print("      </table>");
      }else {?>
      <p>Ingen ukesprogram er registrert ennå.</p>
<?php        
      }
    }else {
        error("WeekPrograms: " . $result->toString());
    }
  }
}
?>