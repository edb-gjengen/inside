<?php

class ProgramsSelection {
  var $conn;

  function ProgramsSelection($limit = 5) {
    $this->__construct($limit);
  }

  function __construct($limit = 5){
    $this->conn = db_connect();
  }

  public function display(){
    $sql = "SELECT id FROM programselection
            ORDER BY id DESC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        $programselection = new ProgramSelection($row->id);
        $programselection->display();
      }
    }else {
      error("ProgramsSelection: " . $result->toString());
    }
    
  }

  public function displayList(){
    $sql = "SELECT id FROM programselection " .
           "ORDER BY id DESC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if($result->numRows() > 0){
?>
      <table class="sortable" id="programselectionlist">
        <tr>
          <th>id</th>
          <th>fra</th>
          <th>til</th>
          <th>type</th>
					<th colspan="3">&nbsp;</th>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
            $programselection = new ProgramSelection($row->id);
            $programselection->displayList();
          }
        print("      </table>");
      }else {?>
      <p>Ingen programutvalg er registrert ennå.</p>
<?php        
      }
    }else {
        error("ProgramsSelection: " . $result->toString());
    }
  }
}
?>