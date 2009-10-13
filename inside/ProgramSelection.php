<?php


/*
 *This class requires PEAR::DB and functions library
 */
class ProgramSelection {

  var $id;
  var $pretext;
  var $start;
  var $end;
  var $type;

  var $conn;

  function ProgramSelection($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New ProgramSelection
      if ($data == NULL){
        error("ProgramSelection: No data supplied.");     
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
    $this->start   = $data['start'];
    $this->end     = $data['end'];
    $this->pretext = ($data['pretext'] == NULL)? '' : $data['pretext'];
    $this->type   = $data['type'];
  }

  public function store(){
    if ($this->id == NULL){
      $sql = sprintf("INSERT INTO programselection 
                          (id, pretext, start, end, type)
                      VALUES 
                          (%s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->pretext),
                     $this->conn->quoteSmart($this->start),
                     $this->conn->quoteSmart($this->end),
                     $this->conn->quoteSmart($this->type)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Programutvalg er registrert.");
      }else {
        error("New ProgramSelection: " . $result->toString());
      }
    }else {
      $sql = sprintf("UPDATE programselection SET " .
      							 "	pretext = %s, " .
      							 "	start   = %s, " .
      							 "	end     = %s, " .
      							 "  type    = %s " .
      							 "WHERE " .
      							 "	id = %s",
                     $this->conn->quoteSmart($this->pretext),
                     $this->conn->quoteSmart($this->start),
                     $this->conn->quoteSmart($this->end),
                     $this->conn->quoteSmart($this->type),
                     $this->conn->quoteSmart($this->id)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Programutvalg er oppdatert.");
      }else {
        error("Update ProgramSelection: " . $result->toString());
      }
    	
    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT *
            FROM programselection
            WHERE id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("ProgramSelection: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM programselection 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      //if ($result->affectedRows() > 0){
        notify("ProgramSelection deleted.");
        // }else {
        //notify("ProgramSelection: invalid id.");        
        // }
    }else {
      error($result->toString());
    }
  }

  public function display(){
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, PROGRAMSELECTION, "program selection", "view-edit-options-programselection");
?>
    </div>
    
<?php
  }

  public function displayList(){
   ?>
      <tr>
        <td>#<?php print $this->id; ?></td>
        <td><?php print $this->start;?></td>
        <td><?php print $this->end;?></td>
        <td><?php print $this->type;?></td>
        <td><a href="index.php?page=edit-program-selection&amp;programselectionid=<?php print $this->id;?>">endre</a></td>
        <td><a href="programutvalg.php?id=<?php print $this->id; ?>&amp;mode=view">vis html</a></td>
        <td><a href="programutvalgtekst.php?id=<?php print $this->id; ?>">vis tekst</a></td>      
      </tr>
    
<?php
  }  
}

?>