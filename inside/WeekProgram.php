<?php


/*
 *This class requires PEAR::DB and functions library
 */
class WeekProgram {

  var $id;
  var $week;
  var $year;
  var $gig1;
  var $gig2;
  var $gig3;
  var $pretext;
  var $posttext;

  var $conn;

  function WeekProgram($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public 
  function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New WeekProgram
      if ($data == NULL){
        error("WeekProgram: No data supplied.");     
      }else {
        //All relevant values are common
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
		    $this->gig1     = $data['gig1'];
    		$this->gig2     = $data['gig2'];
    		$this->gig3     = $data['gig3'];
    		$this->pretext  = ($data['pretext'] == NULL)? '' : $data['pretext'];
    		$this->posttext  = ($data['posttext'] == NULL)? '' : $data['posttext'];
      }
    }

    //Common initializations
    $this->week     = $data['week'];
    $this->year     = $data['year'];
  }

  public 
  function store(){
    if ($this->id == NULL){
      $sql = sprintf("INSERT INTO weekprogram 
                          (id, week, year)
                      VALUES 
                          (%s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->week),
                     $this->conn->quoteSmart($this->year)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Ukesprogram er registrert.");
      }else {
        error("New WeekProgram: " . $result->toString());
        notify("Program for denne uken finnes allerede.");
      }
    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT *
            FROM weekprogram
            WHERE id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("WeekProgram: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM weekprogram 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      //if ($result->affectedRows() > 0){
        notify("WeekProgram deleted.");
        // }else {
        //notify("WeekProgram: invalid id.");        
        // }
    }else {
      error($result->toString());
    }
  }

  public function display(){
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, WEEKPROGRAM, "weekprogram", "view-edit-options-weekprogram");
?>
    </div>
    
<?php
  }

  public function displayList(){
   ?>
      <tr>
        <td>Uke <?php print $this->week . " - " . $this->year; ?></td>
        <td><?php print $this->_firstDay();?></td>
        <td><?php print $this->_lastDay();?></td>
        <td><a href="ukesprogram.php?id=<?php print $this->id; ?>&amp;mode=view" target="_blank">vis</a></td>
        <td><a href="ukesprogram.php?id=<?php print $this->id; ?>&amp;mode=edit" target="_blank">endre</a></td>
        <td><a href="ukesprogramtekst.php?id=<?php print $this->id; ?>" target="_blank">tekst</a></td>
        <td><a href="ukesprogramprint.php?id=<?php print $this->id; ?>" target="_blank">print</a></td>
        <td><a href="ukesprogramuniversitas.php?id=<?php print $this->id; ?>" target="_blank">universitas (onsdag - onsdag)</a></td>
      </tr>
    
<?php
  }  
  
  public
  function _firstDay(){
    $week = $this->week - 1;
    return date("d. M", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $this->year"))));     
  }

  public
  function _lastDay(){
    $week = $this->week;
    return date("d. M", strtotime("last sunday", strtotime("+$week week", strtotime("monday", strtotime(" 1 jan $this->year")))));
    //return date("d. M", strtotime("+$week week", strtotime("sunday", strtotime(" 1$this->year"))));     
  }

}

?>