<?php

class Locations {
  var $locations;
  var $conn;

  function Locations(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect("dns");
    $sql = "SELECT id AS locationId, navn AS locationTitle 
            FROM lokaler
            ORDER BY navn ASC";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->locations = $result;
    }
  }

  public function getList(){
    return $this->locations;
  }

  public function displayList(){
    ?>
      <table class="sortable" id="location-list">
        <tr>
          <th colspan="2">lokaler</th>
          <?php if(checkAuth("view-edit-options-locations")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
     while ($row =& $this->locations->fetchRow(DB_FETCHMODE_ASSOC)){
       $loc = new Location($row['locationId']);
       $loc->displayList();
     }
    print("      </table>");
  }

}
?>