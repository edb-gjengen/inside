<?php

class Document {
  var $id;
  var $type;
  var $name;
  var $size;
  var $date;
  var $documentcategory_id;
  var $tags;
  var $documentcategory_title;

  var $documentData;

  var $conn;
  
  function Document($id = NULL, $documentData = NULL){
    $this->__construct($id, $documentData);
  }
  
  public function __construct($id = NULL, $documentData = NULL){
    $this->conn = db_connect();
    
    if ($id != NULL){
      $this->id = $id;
      if ($data = $this->_retrieveData()){
        $this->type = $data->type;
        $this->name = $data->name;
        $this->size = $data->size;
        $this->date = $data->date;
        $this->tags = $data->tags;
        $this->documentcategory_id    = $data->documentcategory_id;
        $this->documentcategory_title = $data->documentcategory_title;
      }
    }
  }

  public function store($files){
    if ($files != NULL){
      $SrcPathFile = $_FILES["file"]["tmp_name"];   
      $SrcFileType = $_FILES["file"]["type"];   
      $DstFileName = $_FILES["file"]["name"]; 
    
      clearstatcache();   
      
      // File Processing   
      if (file_exists($SrcPathFile)) { 
        // Insert into file table
        $fileid = getNextId("din_document");
        $sql  = sprintf("INSERT INTO din_document 
                            (id, type, name, size, date, documentcategory_id, tags) 
                         VALUES (%s, %s, %s, %s, NOW(), %s, %s)",     
                        $this->conn->quoteSmart($fileid),
                        $this->conn->quoteSmart($SrcFileType),
                        $this->conn->quoteSmart($DstFileName), 
                        $this->conn->quoteSmart(filesize($SrcPathFile)), 
                        $this->conn->quoteSmart(scriptParam("documentcategoryid")),
                        $this->conn->quoteSmart(strtolower(scriptParam("tags"))));
        
        $result =& $this->conn->query($sql);      
        if (DB::isError($result) == true){
          error("Document: " . $result->toString());
        }else {         
          // Insert into the filedata table     
          $fp = fopen($SrcPathFile, "rb");     
          while (!feof($fp)) { 
            // Make the data mysql insert safe       
            $binarydata = fread($fp, 65535); 
            $sql = sprintf("INSERT INTO din_documentdata (document_id, data) VALUES (%s, %s)",
                           $this->conn->quoteSmart($fileid),                      
                           $this->conn->quoteSmart($binarydata)                       
                           );       
            $result =& $this->conn->query($sql);
            if (DB::isError($result) == true){
              error("Document: " . $result->toString());
            }
          } 
          fclose($fp);   
          notify("Document uploaded!");
        }
      } 
    }else {
      error("Document: no file supplied");
    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT d.*, dc.title AS documentcategory_title
            FROM din_document d, din_documentcategory dc
            WHERE d.id = $this->id";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)){
        return $row;
      }else {
        error("Document: document not found.");
      }
    }else {
      error("Document: " . $result->toString());
    }
  }

  public function update($data){
    $conn = db_connect();
    
    if (isset($data['documentid'])){
      $sql = sprintf("UPDATE din_document SET
                          name = %s,
                          documentcategory_id = %s,
                          date = NOW(), " .
                         "tags = %s
                      WHERE
                          id = %s",
                     $conn->quoteSmart($data['name'] . $data['tag']),
                     $conn->quoteSmart($data['documentcategory_id']),
                     $conn->quoteSmart(strtolower($data['tags'])),
                     $conn->quoteSmart($data['documentid'])                     
                     );
      $result =& $conn->query($sql);
      if (DB::isError($result) == true){
        error("Document: " . $result->toString());
      }else {
        notify("Document updated.");
      }
    }
  }

  public function delete($id){
    $conn = db_connect();

    $sql = "DELETE FROM din_document
            WHERE id = $id
            LIMIT 1";
    $result =& $conn->query($sql);
    if (DB::isError($result) == true){
      error("Document: " . $result->toString());
    }else {
      notify("Document deleted.");
    }
    
  }
}
?>