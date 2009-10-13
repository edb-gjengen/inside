<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Group {

  var $id;
  var $name;
  var $text;
  var $division_id;
  var $admin;
  var $mailinglist;

  var $conn;

  function Group($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $conn =& DB::connect(getDSN());
    if (DB::isError($conn)){
      print("error: ".$conn->toString());
      exit();
    }else {
      $this->conn = $conn;
    }

    $this->id = $id;

    if ($this->id == NULL){//New group
      if ($data == NULL){
        error("Group: No data supplied.");     
      }else {
        //All relevant values are common
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
      }
    }
    //Common initializations
    $this->name = $data['name'];
    $this->text = ($data['text']) ? $data['text'] : "...";
    $this->division_id = ($data['division_id'] == "") ? NULL : $data['division_id'];
    $this->admin = $data['admin'];
    $this->mailinglist = $data['mailinglist'];
  }
  
  public function store($actions = NULL){
    $action = scriptParam("action");
    if ($action == "register-group"){
      $sql = sprintf("INSERT INTO din_group 
                          (name, text, division_id, admin, mailinglist)
                      VALUES 
                          (%s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->division_id),
                     $this->conn->quoteSmart($this->admin),
                     $this->conn->quoteSmart($this->mailinglist)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("New group registered and stored.");
      }else {
        error("New group: " . $result->toString());
      }
      
    }else {//Update
      $sql = sprintf("UPDATE din_group SET 
                        name = %s,
                        text = %s,
                        division_id = %s,
                        admin = %s,
                        mailinglist = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->division_id),
                     $this->conn->quoteSmart($this->admin),
                     $this->conn->quoteSmart($this->mailinglist),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
      	
      	//actions
				$sql = "DELETE FROM din_actiongrouprelationship " .
							 "WHERE group_id = $this->id";
	      $result = $this->conn->query($sql);
  	    if (DB::isError($result) != true){
    	  	$keys = array_keys($actions);
      		foreach ($keys as $a) {
      			$prefix = substr($a, 0, 7);
      			$value = substr($a, 7);
	      		if ($prefix == "actions") {
							$agr = new ActionGroupRelationship($value, $this->id);
							$agr->store();
  	    		}
    	  	}
  	    }else {
  	    	error("ActionGroup-delete: " . $result->toString());  	    	
  	    }
        notify("Group updated.");
      }else {
        error("Update group: " . $result->toString());
      }
    }
  }

  public function _retrieveData(){
    $sql = "SELECT g.*
            FROM din_group g
            WHERE g.id = $this->id";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }else {
        error("Groups: " . $result->toString());
      }
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_group WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Group deleted.");
    }else {
      error($result->toString());
    }
  }

  public function display(){
    displayOptionsMenu($this->id, GROUP, "group");
?>
    <h3><?php print "$this->name"; ?></h3>

    <p><?php print prepareForHTML($this->text); ?></p>
    <div class="clear">&nbsp;</div>
    
<?php
  }

  public function displayList(){
   ?>
      <tr>
        <td><a href="index.php?page=display-group&amp;groupid=<?php print $this->id; ?>" 
               title="<?php print $this->text; ?>">
            <?php print $this->name; ?>
        </a></td>
        <td><a href="index.php?page=display-users&amp;groupid=<?php print $this->id;?>"><?php print $this->_getGroupMemberCount(); ?></a></td>
        <?php displayOptionsMenuTable($this->id, GROUP, "group", "view-edit-options-group"); ?>
      </tr>
    
<?php
  }  

  public function _getGroupMemberCount(){
    $sql = "SELECT COUNT(*) FROM din_usergrouprelationship
            WHERE group_id = $this->id";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ORDERED)){
        return $row[0];
      }else {
        error("Count groupmembers: " . $result->toString());
      }
    }    
  }
  
  public
  function isAdmin(){
		return $this->admin;	
	}
}

?>