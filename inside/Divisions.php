<?php

class Divisions {
  var $conn;

  function Divisions() {
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }
  
  public function getList(){
  	if (isAdmin(getCurrentUser()) || checkAuth("all-divisions")){
  		$sql = "SELECT d.id, d.name " .
  					 "FROM din_division d";
  	}else if (loggedIn()){
    	$sql = sprintf("SELECT d.id, d.name " .
    								 "FROM din_division d, din_usergrouprelationship ugr, din_group g ".
        	   				 "WHERE g.division_id = d.id " .
          	 				 "AND g.admin = 1 " .
           					 "AND ugr.group_id = g.id " .
           					 "AND ugr.user_id = %s", 
           					 getCurrentUser());
  	}else {
      $sql = sprintf("SELECT d.id, d.name " .
                     "FROM din_division d " .
                     "WHERE d.hidden != 1 " .
                     "ORDER BY d.name");
    }

    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    } else {
      error("Divisions: " . $result->toString());
    }
  }

  public 
  function getListAll(){
    $sql = "SELECT d.id, d.name " .
             "FROM din_division d";

    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    } else {
      error("Divisions: " . $result->toString());
    }
  }

  public 
  function getListAllWithPositions(){
    $sql = "SELECT d.id, CONCAT(d.name, ' (', COUNT(p.id), ')') AS name " .
    			 "FROM din_division d LEFT JOIN din_position p " .
    			 "ON p.division_id = d.id " .
    			 "GROUP BY d.id " .
    			 "ORDER BY d.name";

    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    } else {
      error("Divisions: " . $result->toString());
    }
  }

  public function display(){
    $sql = "SELECT id FROM din_division
            ORDER BY name ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){

        displayOptionsMenu($row['id'], "division");
        $division = new Division($row['id']);
        $division->display();
      }
    }else {
      error("Divisions: " . $result->toString());
    }   
  }

  public function displayList(){
    $sql = "SELECT id FROM din_division
            ORDER BY name ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){?>
      <table class="sortable" id="divisionlist">
        <tr>
          <th>forening</th>
          <th>telefon</th>
          <th>epost</th>
          <th>kontaktperson</th>
          <?php if(checkAuth("view-edit-options-division")){
        ?><th colspan="2"></th><?php } ?>
        </tr>
<?php      
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        $division = new Division($row['id']);
        $division->displayList();
      }
      print("      </table>");
    }else {
      error("Divisions: " . $result->toString());
    }
  }
}
?>