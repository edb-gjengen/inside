<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Position {

  var $id;
  var $name;
  var $text;
  var $division_id;
  var $division_name;

  var $conn;

  function Position($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New position
      if ($data == NULL){
        error("Position: No data supplied.");     
      }else {
        //All relevant values are common
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        $this->division_name = $data['division_name'];    
      }
    }
    //Common initializations
    $this->name  = $data['name'];
    $this->text  = $data['text'];
    $this->division_id = $data['division_id'];
  }

  public function store(){
    if ($this->id == NULL){
      $sql = sprintf("INSERT INTO din_position 
                          (id, name, text, division_id)
                      VALUES 
                          (%s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->division_id)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Stillingen <strong>$this->name</strong> er registrert.");
      }else {
        error("New position: " . $result->toString());
      }
      
    }else {
      $sql = sprintf("UPDATE din_position SET 
                        name  = %s,
                        text  = %s,
                        division_id = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->division_id),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify(POSITION . "  " . UPDATED);       
      }else {
        error("Update position: " . $result->toString());
      }
    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT p.*, d.name AS division_name
            FROM din_position p, din_division d
            WHERE p.id = $this->id
            AND p.division_id = d.id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Position: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_position 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      //if ($result->affectedRows() > 0){
        notify("Position deleted.");
        // }else {
        //notify("Position: invalid id.");        
        // }
    }else {
      error($result->toString());
    }
  }

  public function display(){
    $ads = $this->_getCurrentAds();
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, POSITION, "position", "view-edit-options-position");
?>
    <h3><?php print $this->name; ?> i <?php print $this->division_name; ?></h3>

    <p><?php print prepareForHTML($this->text); ?></p>
<?php
     if ($ads->numRows() > 0){?>
     <h4>Denne stillingen er for øyeblikket utlyst:</h4>
     <ul>
<?php
       while ($row =& $ads->fetchRow(DB_FETCHMODE_OBJECT)){?>
        <li>
          <a href="index.php?page=display-job&amp;jobid=<?php print $row->id; ?>">
           Søknadsfrist <?php print(formatDatetime($row->expires)); ?></a>
        </li>
<?php
       }?>
     </ul>
<?php       
     }
?>
    </div>
    
<?php
  }

  public function displayList(){
   ?>
      <tr>
        <td><a href="index.php?page=display-division&amp;divisionid=<?php print $this->division_id; ?>"><?php print $this->division_name; ?></a></td>
        <td><a href="index.php?page=display-position&amp;positionid=<?php print $this->id; ?>"><?php print $this->name; ?></a></td>
        <?php displayOptionsMenuTable($this->id, POSITION, "position", "view-edit-options-position"); ?>
      </tr>
    
<?php
  }  

  public function _getCurrentAds(){
    $sql = "SELECT j.id, j.expires
            FROM din_job j
            WHERE j.position_id = $this->id
            AND j.expires > NOW()";

    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    }else {
      error("Position: " . $result->toString());
    }
  }

}

?>