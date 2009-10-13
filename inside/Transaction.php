<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Transaction {

  var $id;
  var $id_string;
  var $user_id;
  var $order_id;
  var $timestamp;
 	var $status;
 	var $amount;

  var $conn;

  function Transaction($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public 
  function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){// Transaction
      if ($data == NULL){
        error("Transaction: No data supplied.");
        return;   
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

	     }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
      	$this->timestamp = $data['timestamp'];
      }
    }
    //Common initializations
    $this->id_string = $data['id_string'];
    $this->user_id   = $data['user_id'];
    $this->order_id  = $data['order_id'];
    $this->status    = $data['status'];
    $this->amount    = $data['amount'];
  }

  public function store(){
    if ($this->id == NULL){
      $this->id = getNextId("din_transaction");
      $sql = sprintf("INSERT INTO din_transaction 
                          (id, id_string, user_id, order_id, timestamp, status, amount)
                      VALUES 
                          (%s, %s, %s, %s, NOW(), %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->id_string),
                     $this->conn->quoteSmart($this->user_id),
                     $this->conn->quoteSmart($this->order_id),
                     $this->conn->quoteSmart($this->status),
                     $this->conn->quoteSmart($this->amount)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        //notify("Ny transaksjon lagret.");
      }else {
        error("New transaction: " . $result->toString());
      }      
    }
  }
  
  public
  function setStatus($value) {
    $sql = "UPDATE din_transaction SET
                status = '$value'
            WHERE " .
           "		id = $this->id";
                   
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      //notify("Transaksjonsstatus oppdatert ($value)");
    }else {
      error("Set status -  transaction: " . $result->toString());
    }  	
  }
  
  public function _retrieveData(){
    $sql = "SELECT *
            FROM din_transaction t
            WHERE t.id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Transaction: " . $result->toString());
    }
  }

  public
  /*static*/ 
  function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_transaction 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      if ($conn->affectedRows() > 0){
        notify("Transaksjonen er slettet.");
      }else {
        notify("Ugyldig transaksjonsid, ingen handling utført.");        
      }
    }else {
      error($result->toString());
    }
  }
  
  public
  /*static*/
  function isFreeIdString($string) {
  	$conn = db_connect();
    $sql = "SELECT id FROM din_transaction 
            WHERE id_string = '$string'";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      if ($result->numRows() == 0){
				return true;
      }
    }else {
      error($result->toString());
    }
		return false;  	
  }
  
  public
  function displayConfirmation() {
  	$order = new Order($this->order_id);
  	?>
  	<h3>Følgende bestilling er registrert og belastet ditt VISA-kort:</h3>
  	<p>Transaksjonen har referansen <strong><?php print $this->id; ?></strong>.</p>
  	<?php
  	$order->displayConfirmation();
  	?>
  	
  	<p>På kontoutskriften vil det stå Payex AS.</p>
  	<p>For spørsmål angående dette kan du kontakte <a href="mailto:support@studentersamfundet.no">support@studentersamfundet.no</a>.</p>
  	<p>Se forøvrig <a href="http://www.studentersamfundet.no/kontakt.php">våre kontaktsider</a> for ytterligere kontaktinformasjon.</p>
  	<?php
  }
}

?>