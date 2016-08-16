<?php

class DocumentCategories {
  var $categories;
  var $conn;

  function DocumentCategories(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();

    $sql = "SELECT id, title FROM din_documentcategory ORDER BY title ASC";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->categories = $result;
    }
  }

  public function getList(){
    return $this->categories;
  }

  public function displayList(){
    ?>
      <table id="document-cat-list">
        <tr>
          <th colspan="2">dokumenttyper</th>
          <?php if(checkAuth("view-edit-options-documentCategory")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
     while ($row =& $this->categories->fetchRow(DB_FETCHMODE_ASSOC)){
       $docCat = new DocumentCategory($row['id']);
       $docCat->displayList();
     }
    print("      </table>");
  }

}
?>