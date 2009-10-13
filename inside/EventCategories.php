<?php

class EventCategories {
  var $categories;
  var $conn;

  function EventCategories(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();

    $sql = "SELECT id, title FROM din_eventcategory";
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
      <table id="event-cat-list">
        <tr>
          <th colspan="2">aktivitetstyper</th>
          <?php if(checkAuth("view-edit-options-eventCategory")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
     while ($row =& $this->categories->fetchRow(DB_FETCHMODE_ASSOC)){
       $eventCat = new EventCategory($row['id']);
       $eventCat->displayList();
     }
    print("      </table>");
  }

}
?>