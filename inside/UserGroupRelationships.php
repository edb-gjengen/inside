<?php

class UserGroupRelationships {
  var $relations;
  var $conn;
  
  function UserGroupRelationships(){
    $this->__construct();
  }

  function __construct(){
      $this->conn = db_connect();
  }    
     
  public function getJoinedList($group_id, $type = "array"){
    $sql = "SELECT ugr.group_id, g.name, u.id, u.username
            FROM din_group g, din_user a LEFT JOIN din_usergrouprelationship ugr
            ON u.user_id = ugr.user_id
            AND ugr.group_id = $group_id
            WHERE g.id = $group_id
            ORDER BY u.firstname, u.lastname";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $list = Array();
      while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        $list["names"][]  = $row->user_id;
        $list["labels"][]  = $row->userName;
        $list["checked"][] = ($row->group_id == NULL) ? false : true;
      }
      return $list;
    }else {
      error($result->toString());
    }
  }

  public function getList($group, $limit){
    $sql = "SELECT user_id, group_id 
            FROM din_usergrouprelationship
            WHERE group_id = $group
            LIMIT $limit";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->relations = $result;
    }else {
      error($result->toString());
    }
    return $this->relations;
  }

  public function displayList($group = 2, $limit = 25){
    $this->getList($group, $limit);
    if ($this->relations->numRows() > 0){
?>
      <table class="sortable" id="usergrouprelationgships">
        <tr>
          <th>medlem</th>
          <th>gruppe</th>
          <?php if(checkAuth("view-edit-options-usergrouprelationship") && $group != 1){
        ?><th></th><?php } ?>
        </tr>
<?php      
        while ($row =& $this->relations->fetchRow(DB_FETCHMODE_OBJECT)){
          $rel = new UserGroupRelationship($row->user_id, $row->group_id);
          $rel->displayList();
        }
      print("      </table>");
    }else {
      print("<p>Ingen gruppemedlemmer er registrert.</p>");
    }
  }

}
?>
