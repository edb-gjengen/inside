<?php

/*
 *This class requires PEAR::DB and functions library
 */
class ConcertReport {

  var $id;
  var $concert_id;
  var $visitors;
  var $marketing_comment;
  var $production_comment;
  var $result;

  var $conn;

  function ConcertReport($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect("dns");

    $this->id = $id;

    if ($id == NULL){//New concert
      if ($data == NULL){
        notify("ConcertReport: No data supplied.");     
      }else {
		    $this->concert_id         = $data['concertid'];

      }
    }else {//ID set, existing concert
      if ($data != NULL){//UPDATE

      }else {//RETRIEVE
        $data = $this->_retrieveData();
		    $this->concert_id         = $data['concert_id'];

      }
    }
    //Common initializations
    $this->visitors           = $data['visitors'];
    $this->marketing_comment  = stripslashes($data['marketing_comment']);
    $this->production_comment = stripslashes($data['production_comment']);
    $this->result             = $data['result'];
  } 

  public
  function store(){       
    if ($this->id == NULL){
      $this->id = getNextId("din_concert_report");

      $sql = sprintf("INSERT INTO din_concert_report 
                          (id, concert_id, visitors, marketing_comment, production_comment, 
                           result)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->concert_id),
                     $this->conn->quoteSmart($this->visitors),
                     $this->conn->quoteSmart($this->marketing_comment),
                     $this->conn->quoteSmart($this->production_comment),
                     $this->conn->quoteSmart($this->result)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) == true){
        error("New ConcertReport: " . $result->toString());
        notify("Problemer med registrering av arrangementsrapport.");
        return false;
      }else {
        notify("Ny arrangementsrapport er registrert.");
        $GLOBALS['extraScriptParams']['concertreportid'] = $this->id;
      }      
    }else {
      $sql = sprintf("UPDATE din_concert_report SET 
                        visitors           = %s,
                        marketing_comment  = %s,
                        production_comment = %s,
                        result             = %s                     
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->visitors),
                     $this->conn->quoteSmart($this->marketing_comment),
                     $this->conn->quoteSmart($this->production_comment),
                     $this->conn->quoteSmart($this->result),
                     $this->conn->quoteSmart($this->id)                     
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) == true){
        error("Update ConcertReport: " . $result->toString());
        notify("Problemer med oppdatering av arrangementsrapport.");
      }else {
        notify("Arrangementsrapport oppdatert.");       
      }
    }
  }

  public function _retrieveData(){
    $sql = "SELECT *
            FROM din_concert_report cr
            WHERE cr.id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }else {
        //notify("Ingen rapportdata er registrert.");
      }
    }else {
      error("ConcertReports: " . $result->toString());
      notify("Problemer med henting av arrangementsrapport.");        
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_concert_report 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Arrangementsrapport slettet.");
    }else {
      error($result->toString());
      notify("Feil under sletting av arrangementsrapport.");
    }
  }

  public function display(){
?>
     <div class="text-column">
	    <h3>Arrangementsrapport</h3>
<?php
    displayOptionsMenu($this->id, CONCERTREPORT, "concertreport", "view-edit-options-concertreport", true, "display-concert");
?>
	    <p>
  	    <strong>Besøkende:</strong> <?php print $this->visitors; ?>
    	</p>
    	<p>
      	<strong>Resultat:</strong> <?php print $this->result; ?>
    	</p>
		  <h4>Om markedsføringen</h4>
  		<p><?php print prepareForHTML($this->marketing_comment); ?></p>
			<h4>Om produksjonen</h4>
  		<p><?php print prepareForHTML($this->production_comment); ?></p>
    </div>
    <div class="clear">&nbsp;</div>
    
<?php
  }
}

?>