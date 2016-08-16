<?php


/*
 *This class requires PEAR::DB and functions library
 */
class ActionGroupRelationship {

  var $action_id;
  var $action_name;
  var $group_id;
  var $group_name;

  var $conn;
  
  function ActionGroupRelationship($action_id = NULL, $group_id = NULL){
    $this->__construct($action_id, $group_id);
  }
  public function __construct($action_id = NULL, $group_id = NULL){
    $this->conn = db_connect();

    $this->action_id = $action_id;
    $this->group_id = $group_id;
    $action = scriptParam("action");
    
    if ($action == "register-actiongrouprelationship" || $action == "update-group"){
      
    }else {
      $data = $this->_retrieveData();
      $this->action_name = $data['action_name'];
      $this->group_name  = $data['group_name'];
    }
  }
  
  public function store(){
    $sql = sprintf("INSERT INTO din_actiongrouprelationship 
                        (action_id, group_id)
                    VALUES 
                        (%s, %s)", 
                   $this->conn->quoteSmart($this->action_id),
                   $this->conn->quoteSmart($this->group_id)
                   );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      if (scriptParam("action") == "register-actiongrouprelationship"){
      	notify("New actiongrouprelationship registered and stored.");
      }
    }else {
      error("New action: " . $result->toString());
    }      
  }

  public function _retrieveData(){
    $sql = "SELECT a.name AS action_name, g.name AS group_name
            FROM din_actiongrouprelationship agr, din_action a, din_group g
            WHERE agr.action_id = $this->action_id
            AND agr.group_id = $this->group_id
            AND agr.action_id = a.id
            AND agr.group_id = g.id";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("ActionGroupRelationships: " . $result->toString());
    }
  }

  public function delete(){
    $sql = "DELETE FROM din_actiongrouprelationship 
            WHERE action_id = $this->action_id 
            AND group_id = $this->group_id
            LIMIT 1";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      notify("ActionGroupRelationship deleted.");
    }else {
      error($result->toString());
    }
  }

  public function displayList(){
   ?>
      <tr>
        <td><?php print "$this->action_name"; ?></td>
        <td><?php print "$this->group_name"; ?></td>
      </tr>
    
<?php
  }  
}

?>
