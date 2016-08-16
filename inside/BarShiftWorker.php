<?php


/*
 *This class requires PEAR::DB and functions library
 */
class BarShiftWorker {

  var $barshift_id;
  var $barshift_title;
  var $user_id;
  var $user_name;
  var $registered;

  var $conn;
  
  function BarShiftWorker($barshift_id = NULL, $user_id = NULL){
    $this->__construct($barshift_id, $user_id);
  }
  public function __construct($barshift_id = NULL, $user_id = NULL){
    $this->conn = DB_connect();

    $this->barshift_id = $barshift_id;
    $this->user_id = $user_id;
    $action = scriptParam("action");
    
    if ($action == "register-barshiftworker"){
      
    }else {
      $data = $this->_retrieveData();
      $this->barshift_title= $data['barshift_title'];
      $this->user_name  = $data['user_name'];
      $this->registered = $data['registered'];
    }
  }
  
  public function store(){
    $action = scriptParam("action");
    if ($action == "register-barshiftworker"){
      $sql = sprintf("INSERT INTO din_barshiftworker
                          (barshift_id, user_id, registered)
                      VALUES 
                          (%s, %s, NOW())", 
                     $this->conn->quoteSmart($this->barshift_id),
                     $this->conn->quoteSmart($this->user_id)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Barvakt er registrert.");
      }else {
        $user = new User($this->user_id);
        notify("$user->firstname $user->lastname er allerede satt opp på denne vakten.");
        error("New barshiftworker: " . $result->toString());
      }      
    }
  }

  public function _retrieveData(){
    $sql = "SELECT bsw.*, b.title AS barshift_title, CONCAT(u.firstname, ' ', u.lastname) AS user_name
            FROM din_barshiftworker bsw, din_barshift b, din_user u
            WHERE bsw.barshift_id = $this->barshift_id
            AND bsw.user_id = $this->user_id
            AND bsw.barshift_id = b.id
            AND bsw.user_id = u.id";
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("BarShiftWorkers: " . $result->toString());
    }
  }

  public 
  function delete($barshift_id, $user_id){
    $conn = db_connect();
    $sql = "DELETE FROM din_barshiftworker 
            WHERE barshift_id = $barshift_id 
            AND user_id = $user_id
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Oppsatt barvakt slettet.");
    }else {
      error($result->toString());
    }
  }

  public function displayList(){
   ?>
      <tr>
        <td><?php print $this->barshift_name; ?></td>
        <td><?php print $this->user_name; ?></td>
      </tr>
    
<?php
  }  
}

?>