<?php

class Jobs {
  var $conn;

  function Jobs($selection = "0000-00-00"){
    $this->__construct($selection);
  }

  function __construct($selection = "0000-00-00"){
    $this->conn = db_connect();
  }
  
  public function display($selection = "0000-00-00"){
    $sql = "SELECT id FROM din_job
            ORDER BY published DESC
            WHERE expires > $selection";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
    	if ($result->numRows() > 0){
	      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
  	      displayOptionsMenu($row['id'], "job");
    	    $job = new Job($row['id']);
      	  $job->display();
      	}	
    	}else {
    		print("Ingen stillinger er registrert.");
    	}
    }else {
      error("Jobs: " . $result->toString());
    }
    
  }

  public function displayList($selection = "0000-00-00"){
    $sql = "SELECT id FROM din_job
            WHERE expires > $selection
            ORDER BY published DESC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
    	if ($result->numRows() > 0){?>
      <p>Antall treff: <?php print $result->numRows(); ?> </p>
      <table class="sortable" id="joblist">
        <tr>
          <th>&nbsp;</th>
          <th>tittel</th>
          <th>type</th>
          <th>forening</th>
          <th>publisert</th>
          <th>utgår</th>
          <?php if(checkAuth("view-edit-options-job")){
        ?><th colspan="2"></th><?php } ?>
        </tr>
<?php      
    	}else {
    		print("Ingen stillinger er registrert.");    		
    	}
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        $job = new Job($row['id']);
        $job->displayList();
      }
      print("      </table>");
    }else {
      error("Jobs: " . $result->toString());
    }
    
  }

}


?>
