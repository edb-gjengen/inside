<?php

class DivisionCategories {
  var $categories;
  var $conn;

  function DivisionCategories(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();

    $sql = "SELECT id, title FROM din_divisioncategory";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->categories = $result;
    }
  }

  public function getCategories(){
    return $this->categories;
  }

  public function displayList(){
    ?>
      <table class="sortable" id="division-cat-list">
        <tr>
          <th colspan="2">foreningstyper</th>
          <?php if(checkAuth("view-edit-options-divisioncategory")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
     while ($row =& $this->categories->fetchRow(DB_FETCHMODE_ASSOC)){
       $divisionCat = new DivisionCategory($row['id']);
       $divisionCat->displayList();
     }
    print("      </table>");
  }

}
?>