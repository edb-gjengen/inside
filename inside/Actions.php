<?php

class Actions {
  var $actions;
  var $conn;

  function Actions(){
    $this->__construct();
  }

  function __construct(){
    $conn =& DB::connect(getDSN());
    if (DB::isError($conn)){
      error("Actions: " . $conn->toString());
      exit();
    }else {
      $this->conn = $conn;
    }

    $sql = "SELECT id, name  
            FROM din_action
            ORDER BY name ASC";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->actions = $result;
    }else {
      error($result->toString());
    }
  }

  public function getList(){
    return $this->actions;
  }

  public function displayList($limit = 25){
    if ($this->actions->numRows() > 0){
?>
      <table class="sortable" id="actions">
        <tr>
          <th>handling</th>
          <th>beskrivelse</th>
          <th colspan="2">&nbsp;</th>
        </tr>
<?php      
        while ($row =& $this->actions->fetchRow(DB_FETCHMODE_OBJECT)){
          $action = new Action($row->id);
          $action->displayList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen handlinger registrert.</p>");
    }
  }

}
?>