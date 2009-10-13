<?php

class PlacesOfStudy {
  var $users;
  var $conn;
  
  function PlacesOfStudy(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = DB_connect("dns");

    $minId = 1;   
    if (loggedIn()){
      $user = new User(getCurrentUser());
      if ($user->placeOfStudy == 1){
        $minId = 0; 
      } 
    }

    $sql = "SELECT id, navn AS name " .
           "FROM studiesteder " .
           "WHERE id > $minId " .
           "ORDER BY name ASC";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->users = $result;
    }else {
      error($result->toString());
    }
  }

  public function getList(){
    return $this->users;
  }

  public function displayList($limit = 25){
    if ($this->users->numRows() > 0){
?>
      <table class="sortable" id="userlist">
        <tr>
          <th>id</th>
          <th>navn</th>
          <th colspan="2">&nbsp;</th>
        </tr>
<?php      
        while ($row =& $this->users->fetchRow(DB_FETCHMODE_OBJECT)){
?>
      <tr>
        <td><a href="index.php?page=display-user&amp;userid=<?php print $row->userId; ?>">
            <?php print $row->userId; ?></a></td>
        <td><a href="index.php?page=display-user&amp;userid=<?php print $row->userId; ?>">
            <?php print $row->name; ?></a></td>
        <?php displayOptionsMenuTable($row->userId, "user"); ?>
      </tr>
    

<?php          
        }
      print("      </table>");
    }else {
      print("<p>Ingen medlemmer registrert.</p>");
    }
  }

}
?>