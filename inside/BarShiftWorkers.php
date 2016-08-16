<?php

class BarShiftWorkers {
  var $relations;
  var $conn;

  function BarShiftWorkers(){
    $this->__construct();
  }
  
  function __construct(){
    $this->conn = DB_connect();
  }    
     
	public
	function _getListByUser($user_id){
		$sql = "SELECT * FROM din_barshiftworker " .
					 "WHERE user_id = $user_id";
					 
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      return $result;
     }else {
      error("BarShiftWorkers: " . $result->toString());
    }
	}

	public
	function _getListByBarShift($barshift_id){
		$sql = "SELECT * FROM din_barshiftworker " .
					 "WHERE barshift_id = $barshift_id";
					 
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      return $result;
     }else {
      error("BarShiftWorkers: " . $result->toString());
    }
	}
	
	public
	function displayListByUser($user_id){
		$list = $this->_getListByUser($user_id);
	
	  if ($result->numRows() > 0){
			$user = new User($user_id);
?>
      <h2>Vakter for <?php print $user->firstname." ".$user->lastname; ?></h2>
      <table class="sortable" id="barshiftworkerlist">
        <tr>
          <th>tittel</th>
          <th>sted</th>
          <th>dato</th>
          <th>start</th>
          <th>slutt</th>
          <th>ledige</th>
          <?php if(checkAuth("view-edit-options-barshift")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $list->fetchRow(DB_FETCHMODE_ASSOC)){
            $barshift = new BarShift($row['id']);
            $barshift->displayList();
          }
        print("      </table>");
      }else {
        print("<p>Ingen vakter registrert.</p>");
      }	
	}
	
	public
	function displayListByBarShift($barshift_id){
		$list = $this->_getListByBarShift($barshift_id);
	
	  if ($list->numRows() > 0){
?>
      <p>På jobb:</p>
      <table class="sortable" id="barshiftworkerlist">
        <tr>
          <th>navn</th>
          <th>telefon</th>
          <th>registrert</th>
          <?php if(checkAuth("view-edit-options-barshiftworker")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $list->fetchRow(DB_FETCHMODE_OBJECT)){
						$user = new User($row->user_id);
					?>
					<tr>
						<td><?php print $user->firstname." ".$user->lastname; ?></td>
						<td><?php print formatPhone($user->phonenumber); ?></td>
						<td><?php print formatDatetimeShort($row->registered); ?></td>
						<?php if (checkAuth("view-edit-options-barshiftworker")){
							displayOptionsMenuTable(Array("barshift_id" => $barshift_id, "user_id" => $user->id), BARSHIFTWORKER, "barshiftworker", "view-edit-options-barshiftworker", false, "barshift");
						}?>
					</tr>						
					<?php
          }
        print("      </table>");
    }
	}	

	public
	function displayCalendarListByBarShift($barshift_id){
		$list = $this->_getListByBarShift($barshift_id);
	
	  if ($list->numRows() > 0){
    	print('<ul class="barshift-workers">');
    	while ($row =& $list->fetchRow(DB_FETCHMODE_OBJECT)){
				$user = new User($row->user_id);
				print "<li>".$user->firstname." ".$user->lastname." - ".formatPhone($user->phonenumber)."</li>";
      }
    	print("</ul>");
    }
	}	
}
?>