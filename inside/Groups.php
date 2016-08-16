<?php

class Groups {
  var $groups;
  var $conn;

  function Groups($restriction = NULL) {
    $this->__construct($restriction);
  }

  function __construct($restriction = NULL){
    $this->conn = db_connect();

    if (!checkAuth("view-protected-users")){
      $condition = "AND id != 1";
    }else {
      $condition = "";
    }
    if (isAdmin() == true || checkAuth("view-protected-users")){
      $sql = "SELECT id, name FROM din_group";
    }else if ($restriction == "admin-only"){
    	$sql = sprintf("SELECT DISTINCT g.id, g.name FROM din_group g, din_group g2, din_division d, din_usergrouprelationship ugr " .
      			 				 "WHERE g.division_id = d.id " .
      			 				 "AND g2.division_id = d.id " .
      			 				 "AND ugr.group_id = g2.id " .
      			 				 "AND g2.admin = 1 " .
      			 				 "AND ugr.user_id = %s " .
      			 				 "ORDER BY name",
      			 				 getCurrentUser());
    }else {
			$sql = "SELECT id, name " .
						 "FROM din_group " .
						 "WHERE id != 1 " .
						 "ORDER BY name";
    }
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->groups = $result;
    }else {
      error($result->toString());
    }
  }

  public function getList(){
    return $this->groups;
  }

  public function displayList($limit = 25){
    if ($this->groups->numRows() > 0){
?>
      <table class="sortable" id="groups">
        <tr>
          <th>gruppenavn</th>
          <th>medlemmer</th>
          <th colspan="2">&nbsp;</th>
        </tr>
<?php      
        while ($row =& $this->groups->fetchRow(DB_FETCHMODE_OBJECT)){
          $group = new Group($row->id);
          $group->displayList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen grupper registrert.</p>");
    }
  }

}
?>
