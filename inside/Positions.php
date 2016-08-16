<?php

class Positions {
  var $conn;

  function Positions() {
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }

  public function getList($division = NULL){
    if ($division != NULL){
       $condition = "AND p.division_id = $division"; 
    }else {
       $condition = ""; 
    }
    $sql = "SELECT d.id AS divId, d.name AS divName, p.id AS positionId, p.name AS positionName " .
           "FROM din_position p, din_division d " .
           "WHERE p.division_id = d.id " .
           "$condition" .
           "ORDER BY d.name ASC";

    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    } else {
      error("Positions: " . $result->toString());
      return false;
    }
  }
  
  public function getPositionsAsArray(){
    if ($list = $this->getPositions()){
      $pos = Array();
      while ($row =& $list->fetchRow(DB_FETCHMODE_ASSOC)){
        $div = $row['divId'];
        
        $pos[] = Array("id" => $row['positionId'],
                       "title" => $row['positionName'],
                       "parent" => $row['positionDivId']);
      }
      return $pos;
    }
  }

  public function displayList($division){
    $sql = "SELECT p.id FROM din_position p, din_division d " .
           "WHERE p.division_id = d.id " .
           "AND p.division_id = $division " .
           "ORDER BY d.name ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if($result->numRows() > 0){
?>
      <table class="sortable" id="positionlist">
        <tr>
          <th>forening</th>
          <th>stilling</th>
          <?php if(checkAuth("view-edit-options-position")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
            $position = new Position($row['id']);
            $position->displayList();
          }
        print("      </table>");
      }else {?>
      <p>Ingen stillingsbeskrivelser er registrert ennå.</p>
<?php        
      }
    }else {
        error("Positions: " . $result->toString());
    }
  }
}
?>