<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Action {

  var $id;
  var $name;
  var $text;

  var $conn;

  function Action($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }
  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($this->id == NULL){//New action
      if ($data == NULL){
        error("Action: No data supplied.");     
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
    $this->text = $data['text'];
  }
  
  public function store(){
    $action = scriptParam("action");
    if ($action == "register-action"){
    	$this->id = getNextId("din_action");
      $sql = sprintf("INSERT INTO din_action VALUES " .
      							 "  (%s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("New action registered and stored.");
        $sql = "INSERT INTO din_actiongrouprelationship VALUES 
                  ($this->id, 3)";
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          notify("Action granted to administrator group.");          
        }else {
          error("Grant action: " . $result->toString());
        }
      }else {
        error("New action: " . $result->toString());
      }
      
    }else {//Update
      $sql = sprintf("UPDATE din_action SET 
                        name = %s,
                        text = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Action updated.");
      }else {
        error("Update action: " . $result->toString());
      }
    }
  }

  public function _retrieveData(){
    $sql = "SELECT a.*
            FROM din_action a
            WHERE a.id = $this->id";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }else {
        error("Actions: " . $result->toString());
      }
    }
  }

  public function delete($id){
    $conn = db_connect();

    $sql = "DELETE FROM din_action
            WHERE id = $id
            LIMIT 1";
    $result =& $conn->query($sql);
    if (DB::isError($result) == true){
      error("Action: " . $result->toString());
    }else {
      notify("Action deleted.");
    }
    
  }

  public function display(){
    displayOptionsMenu($this->id, "action");
?>
    <h3><?php print "$this->name"; ?></h3>

    <p><?php print prepareForHTML($this->text); ?></p>
    <div class="clear">&nbsp;</div>
    
<?php
  }

  public function displayList(){
   ?>
      <tr>
        <td><a href="index.php?page=display-action&amp;actionid=<?php print $this->id; ?>"><?php print $this->name; ?></a></td>
        <td><?php print prepareForHTML($this->text); ?></td>
        <?php displayOptionsMenuTable($this->id, ACTION, "action", "view-edit-options-action"); ?>
      </tr>
    
<?php
  }  
}

?>