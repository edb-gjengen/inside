<?php


/*
 *This class requires PEAR::DB and functions library
 */
class nordea {

  var $id;
  var $firstname;
  var $lastname;
  var $email;
  var $phonenumber;

  var $conn;

  function Nordea($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New Nordea-lead
      if ($data == NULL){
        error("Nordea-lead: No data supplied.");     
      }
    }else {//ID set, existing lead
      if ($data != NULL){//Update existing lead
      
      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
      }
    }
    //Common initializations
    $this->firstname   = $data['firstname'];
    $this->lastname    = $data['lastname'];
    $this->email       = $data['email'];    
    $this->phonenumber = $data['phonenumber'];
  }

  public function store(){
    if ($this->id == NULL){
      $sql = sprintf("INSERT INTO din_nordea 
                          (id, firstname, lastname, email, phonenumber)
                      VALUES 
                          (%s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->firstname),
                     $this->conn->quoteSmart($this->lastname),
                     $this->conn->quoteSmart($this->email),
                     $this->conn->quoteSmart($this->phonenumber)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Ønske om å bli kontakt av Nordea er registrert.");
        $sendto = "student@nordea.no";        
        $subject = "Nytt lead fra Studentersamfundet";
        $message = "Dette er en autogenerert epost som blir sendt hver gang et nytt medlem har haket av for ønske om å bli kontaktet av Nordea. Denne personen har nettopp kjøpt medlemskap i Studentersamfundet og registrert seg på nettsidene våre." .
                 "\n" .
                 "\nEt nytt lead er registrert:" .
                 "\n" .
                 "\n$this->firstname $this->lastname" .
                 "\n" .
                 "\nEpost: $this->email".
                 "\nTelefon: $this->phonenumber" .
                 "\n" .
                 "\nmvh" .
                 "\nStudentersamfundet";
        $headers = 'From: nordea@studentersamfundet.no'."\r\n";
        $headers .= 'Cc: support@studentersamfundet.no'."\r\n";
        mail($sendto, $subject, $message, $headers);
        
      }else {
        error("New nordea: " . $result->toString());
        notify("Problemer med å registrere Nordealead.");
      }
          
    }else {
      //Not finished
      $sql = sprintf("UPDATE din_nordea SET 
                        name                = %s,
                        text                = %s,
                        phone               = %s,
                        email               = %s,
                        office              = %s,
                        user_id_contact     = %s,
                        url                 = %s,
                        nordeacategory_id = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->phone),
                     $this->conn->quoteSmart($this->email),
                     $this->conn->quoteSmart($this->office),
                     $this->conn->quoteSmart($this->user_id_contact),
                     $this->conn->quoteSmart($this->url),
                     $this->conn->quoteSmart($this->nordeacategory_id),
                     $this->conn->quoteSmart($this->id)
                     );

//      $result = $this->conn->query($sql);
//      if (DB::isError($result) != true){
//        notify("Nordea-lead oppdatert.");
//      }else {
//        error("Update nordea lead: " . $result->toString());
//      }

    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT *
            FROM din_nordea
            WHERE id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
				$row['text'] = stripslashes($row['text']);      
        return $row;
      }
    }else {
      error("nordea: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_nordea 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      if ($conn->affectedRows() > 0){
        notify("Nordea-lead slettet.");
      }else {
        notify("Ugyldig lead-id, ingen handling utført.");        
      }
    }
  }

  public function display(){

  }

  public function displayList(){

  }  

}

?>