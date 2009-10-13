<?php

class Articles {
  private $conn;
  private $list;

  function __construct(){
    $this->conn = db_connect();
  }

  public function displayList($limit = 25){
    $sql = "SELECT id
            FROM nyhet
            ORDER BY dato DESC LIMIT $limit";
    if ($this->conn == false){
      return false;
    }
    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($result->numRows() > 0){
			print("<p>Antall treff: ".$result->numRows()."</p>");
?>
      <table class="sortable" id="articlelist">
        <tr>
          <th>tittel</th>
          <th>intro</th>
          <th>forfatter</th>
          <th>publisert</th>
          <th>utgår</th>
          <?php if(checkAuth("view-edit-options-article")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
            $article = new Article($row['id']);
            $article->displayList();
          }
        print("      </table>");
      }else {
        print("<p>Ingen nyheter er registrert.</p>");
      }
    }else {
      error("Articles: " . $result->toString());
    }
  }
}


?>