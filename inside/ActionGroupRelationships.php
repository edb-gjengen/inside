<?php

class ActionGroupRelationships {
  var $relations;
  var $conn;

  function ActionGroupRelationships(){
    $this->__construct();
  }
  
  function __construct(){
      $this->conn = db_connect();
  }    
     
  public function getJoinedList($group_id, $type = "array", $exclude = 1){
  	$exclude_statement = "";
  	if (is_array($exclude)) {
  		foreach ($exclude as $e) {
  			if ($e != 0) {
  				$exclude_statement .= "AND a.id NOT IN (SELECT action_id FROM din_actiongrouprelationship agr WHERE agr.group_id = $e)\n";
  			}
  		}
  	}else {
  		$exclude_statement = "AND a.id NOT IN (SELECT action_id FROM din_actiongrouprelationship agr WHERE agr.group_id = $exclude)";
  	}	
  
    $sql = "SELECT agr.group_id, a.id AS action_id, a.name AS action_name
            FROM din_group g, din_action a LEFT JOIN din_actiongrouprelationship agr
            ON a.id = agr.action_id
            AND agr.group_id = $group_id
            WHERE g.id = $group_id
						$exclude_statement
            ORDER BY a.name";
		
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $list = Array();
      while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        $list["names"][]   = $row->action_id;
        $list["labels"][]  = $row->action_name;
        $list["checked"][] = ($row->group_id == NULL) ? false : true;
      }
      return $list;
    }else {
      error($result->toString());
    }
  }

  public function getList(){
    $sql = "SELECT action_id, group_id 
            FROM din_actiongrouprelationship";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->relations = $result;
    }else {
      error($result->toString());
    }
    return $this->relations;
  }

  public function displayList($limit = 25){
    $this->getList();
    if ($this->relations->numRows() > 0){
?>
      <table class="sortable" id="actiongrouprelationgships">
        <tr>
          <th>handling</th>
          <th>gruppe</th>
        </tr>
<?php      
        while ($row =& $this->relations->fetchRow(DB_FETCHMODE_OBJECT)){
          $rel = new ActionGroupRelationship($row->action_id, $row->group_id);
          $rel->displayList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen forhold registrert.</p>");
    }
  }

}
?>
