<?php

class DocumentCategory {
  var $id;
  var $title;
  var $text;
  var $conn;

  function DocumentCategory($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();
    $this->id = $id;

    if ($id == NULL){//New event
      if ($data == NULL){
        error("DocumentCategory: No data supplied.");     
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
    $this->title = $data['title'];
    $this->text  = $data['text'];
  }

  public function store(){
    $this->conn->autoCommit(false);            
    if ($this->id == NULL){
      $this->id = getNextId('din_documentcategory');
      $sql = sprintf("INSERT INTO din_documentcategory VALUES
                      (%s, %s, %s)",
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart(scriptParam("title")),
                     $this->conn->quoteSmart(scriptParam("text"))
                   );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $GLOBALS['extraScriptParams']['documentcategoryid'] = $this->id;
        if ($this->conn->commit()){
          notify("New Document Category registered and stored.");
        }else {
          $this->conn->rollback();
          error("New Document Category: " . $result->toString());
        }
      }else {
        $this->conn->rollback();
        error("New Document Category: " . $result->toString());
      }
    }else {
      $sql = sprintf("UPDATE din_documentcategory SET 
                        title = %s,
                        text  = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        if ($this->conn->commit()){
          notify("Document Category updated.");
        }else {
          $this->conn->rollback();
          error("New Document Category: " . $result->toString());
        }
      }else {
        error("Update Document Category: " . $result->toString());
      }        
    }
  }

  public function _retrieveData(){
    $sql = "SELECT *
            FROM din_documentcategory dc
            WHERE id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Document category: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_documentcategory 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Document category deleted.");
    }else {
      error($result->toString());
    }
  }

  public function displayList(){

   ?>
      <tr id="documentcategory<?php print $this->id; ?>">
        <td><?php print $this->title; ?></td>
        <td><?php print prepareForHTML($this->text); ?></td>
        <?php displayOptionsMenuTable($this->id, DOCCAT, 
        "documentcategory", "view-edit-options-documentCategory"); ?>
      </tr>
    
<?php
  }  

}
?>