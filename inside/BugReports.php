<?php

class BugReports {
  var $bugs;
  var $conn;

  function BugReports($selection = "all"){
    $this->__construct($selection);
  }

  function __construct($selection = "all"){
	  $this->conn = db_connect();

		switch ($selection) {
			case "all":
			$cond = "";
			break;
			
			case "new":
			$cond = "WHERE active = 1";
			break;
			
			case "old":
			$cond = "WHERE active != 1";
			
			default:
			$cond = "";
		}
    $sql = "SELECT id FROM din_bugreport $cond";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->bugs = $result;
    }
  }

  public 
  function getList(){
    return $this->bugs;
  }

  public
  function displayList(){
    ?>
      <table id="bugreports" class="sortable">
        <tr>
          <th>title</th>
          <th>comment</th>
          <th>user</th>
        </tr>
<?php      
     while ($row =& $this->bugs->fetchRow(DB_FETCHMODE_OBJECT)){
       $bug = new BugReport($row->id);
       $bug->displayList();
     }
    print("      </table>");
  }
}
?>