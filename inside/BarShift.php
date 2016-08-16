<?php


/*
 *This class requires PEAR::DB and functions library
 */
class BarShift {

  var $id;
  var $date;
  var $location_id;
  var $location_name;
  var $start;
  var $end;
  var $num_workers;
  var $sum_workers;
  var $title;
  var $eventcategory_title;

  var $conn;

  function BarShift($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New BarShift
      if ($data == NULL){
        error("BarShift: No data supplied."); 
      }else {
        //All relevant values are common
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
		    $this->location_name = $data['location_name'];
      }
    }
    //Common initializations
    $this->date   		 = $data['date'];
    $this->start       = formatTime($data['start']);
    $this->end				 = formatTime($data['end']);
    $this->location_id = $data['location_id'];
    $this->title			 = $data['title'];
    $this->num_workers = (is_numeric($data['num_workers']))? $data['num_workers'] : 0;
  }

  public function store(){
    $this->conn->autoCommit(false);            
    if ($this->id == NULL){
      $this->id = getNextId("din_barshift");
      $sql = sprintf("INSERT INTO din_barshift 
                          (id, date, location_id, start, end, num_workers, title)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->date),
                     $this->conn->quoteSmart($this->location_id),
                     $this->conn->quoteSmart($this->start),
                     $this->conn->quoteSmart($this->end),
                     $this->conn->quoteSmart($this->num_workers),
                     $this->conn->quoteSmart($this->title)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $GLOBALS['extraScriptParams']['barshiftid'] = $this->id;
        $sql = sprintf("INSERT INTO din_barshiftupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart(scriptParam('barshiftid')),
                       $this->conn->quoteSmart(getCurrentUser()),                           
                       $this->conn->quoteSmart("Bar shift registered.")
                      );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("Ny barvakt er registert.");
          }else {
            $this->conn->rollback();
            error("New Bar Shift: " . $result->toString());
          }
        }else {
            $this->conn->rollback();
            error("New Bar Shift: " . $result->toString());
        }
      }else {
        $this->conn->rollback();
        error("New Bar Shift: " . $result->toString());
      }
    }else {
      $sql = sprintf("UPDATE din_barshift SET 
                        date        = %s,
                        location_id = %s,
                        start       = %s,
                        end         = %s,
                        num_workers = %s," .
                     "  title       = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->date),
                     $this->conn->quoteSmart($this->location_id),
                     $this->conn->quoteSmart($this->start),
                     $this->conn->quoteSmart($this->end),
                     $this->conn->quoteSmart($this->num_workers),
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $sql = sprintf("INSERT INTO din_barshiftupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart($this->id),
                       $this->conn->quoteSmart(getCurrentUser()),                           
                       $this->conn->quoteSmart("Bar shift updated.")
                       );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("Barvakt oppdatert.");
          }else {
            $this->conn->rollback();
            error("New BarShift: " . $result->toString());
          }
        }else {
          error("Update BarShift: " . $result->toString());
        }        
      }else {
          error("Update BarShift: " . $result->toString());
      }
    }
  }

  public function repeat($data){
    $frequency = $data['frequency'];
    $count = $data['count'];
    for ($i = 0; $i < $count; $i++){
 	    $id = getNextId("din_barshift");
      $date = get_repeat_date($this->date, $i + 1, $frequency);
      $sql = sprintf("INSERT INTO din_barshift
                          (id, date, location_id, start, end, num_workers, title)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($id),
                     $this->conn->quoteSmart($date),
                     $this->conn->quoteSmart($this->location_id),
                     $this->conn->quoteSmart($this->start),
                     $this->conn->quoteSmart($this->end),
                     $this->conn->quoteSmart($this->num_workers),
                     $this->conn->quoteSmart($this->title)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
      	$sql = sprintf("INSERT INTO din_barshiftupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart($id),
                       $this->conn->quoteSmart(getCurrentUser()),                           
                       $this->conn->quoteSmart("Bar shift registered (repeat).")
                       );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("Barvakt repetert.");
          }else {
            $this->conn->rollback();
            error("New BarShift: " . $result->toString());
          }
        }else {
        	$this->conn->rollback();
          error("New BarShift: " . $result->toString());
        }      	
      }else {
        error("New BarShift: " . $result->toString());
      }
    }
  }

  public function copy($data){
    $id = getNextId("din_barshift");
    $sql = sprintf("INSERT INTO din_barshift
                          (id, date, location_id, start, end, num_workers, title)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($id),
                     $this->conn->quoteSmart($data['new-date']),
                     $this->conn->quoteSmart($this->location_id),
                     $this->conn->quoteSmart($this->start),
                     $this->conn->quoteSmart($this->end),
                     $this->conn->quoteSmart($this->num_workers),
                     $this->conn->quoteSmart($this->title)
                     );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $sql = sprintf("INSERT INTO din_barshiftupdate 
                      VALUES (NULL, NOW(), %s, %s, %s)",
                     $this->conn->quoteSmart($id),
                     $this->conn->quoteSmart(getCurrentUser()),                           
                     $this->conn->quoteSmart("Bar shift registered (copy).")
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
      	if ($this->conn->commit()){
          notify("Barvakt kopiert.");
			    $GLOBALS['extraScriptParams']['barshiftid'] = $id;
        }else {
          $this->conn->rollback();
          error("New BarShift: " . $result->toString());
        }
      }
    }else {
      notify("Problemer med kopiering av barvakt.");
      error("New BarShift: " . $result->toString());
    }
  }
  

  public function _retrieveData(){
    $sql = "SELECT b.*, l.navn AS location_name 
            FROM din_barshift b, lokaler l " .
           "WHERE b.id = $this->id 
            AND b.location_id = l.id";
            
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("BarShifts: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_barshift 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Barvakt slettet.");
      if (scriptParam("barshift-overview") != "") {
      	$GLOBALS['extraScriptParams']['page'] = scriptParam("barshift-overview");
      }
    }else {
      error($result->toString());
      notify("Barvakten kan ikke slettes. Dette er en feil. Vennligst rapporter problemet med skjemaet under:");
      reportBug("delete-barshift");
    }
  }
   
  public function display(){
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, BARSHIFT, "barshift", "view-edit-options-barshift");
?>
    <h3><?php print $this->title." - ". $this->location_name; ?></h3>
    <p><?php print formatDate($this->date); ?></p>
    <p><?php print $this->start." - ".$this->end; ?></p>
    <p><?php print $this->_getSumWorkers()." av ".$this->num_workers." vakter er dekket."; ?></p>		 
	<?php
		if (checkAuth("perform-register-barshiftworker")){
			$users = new Users();
			$userList = $users->getList(13);//13 == Tappetårnet 
		
			$userid = scriptParam("userid");
			if ($userid != ""){
				$cur = $userid;	
			}else {
				$cur = getCurrentUser();
			}

      $title   = "register bar shift worker";
 	    $enctype = NULL;
   	  $method  = "post";
      $action  = "index.php?action=register-barshiftworker&amp;page=display-barshift";
 	    $fields  = Array(Array("label" => "barshiftidid", "type" => "hidden",
   	                         "attributes" => Array("name" => "barshiftid", "value" => "$this->id")),
     	                 Array("label" => "sett opp", "type" => "select", 
       	                     "attributes" => Array("name" => "userid", "values" => $userList,
         	                                         "currentValue" => $cur))
           	           );
      $form = new Form($title, $enctype, $method, $action, $fields);
 	    $form->display("horizontal");
		}

		$this->_displayWorkerList();

    if (checkAuth("view-register-barshift")){
    	?><p>Repetisjon og kopiering kopierer bare informasjon om vakten, ikke de som er satt opp på vakt.</p><?php
      $freqOptions = Array(Array("id" => "daily", "title" => "daglig"), 
                           Array("id" => "weekly", "title" => "ukentlig"),
                           Array("id" => "biweekly", "title" => "annenhver uke"),
                           Array("id" => "monthlyDate", "title" => "månedlig"),
                           Array("id" => "annual", "title" => "årlig")
                               );
                           
      $title   = "repeat bar shift";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=repeat-barshift&amp;page=display-barshift";
      $fields  = Array(Array("label" => "barshiftidid", "type" => "hidden",
                             "attributes" => Array("name" => "barshiftid", "value" => "$this->id")),
                       Array("label" => "gjenta vakt", "type" => "select", 
                             "attributes" => Array("name" => "frequency", "values" => $freqOptions,
                                                   "value" => $freqOptions[0])),
                       Array("label" => "antall ganger", "type" => "text",
                             "attributes" => Array("name" => "count", "size" => 3, "maxlength" => 3,
                                                   "value" => 0))
                       );
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");


      $title   = "copy bar shift";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=copy-barshift&amp;page=display-barshift";
      $fields  = Array(Array("label" => "barshiftid", "type" => "hidden",
                             "attributes" => Array("name" => "barshiftid", "value" => "$this->id")),
                       Array("label" => "kopier til", "type" => "date", 
                             "attributes" => Array("name" => "new-date", "value" => "$this->date"))
                       );
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");
    }

    ?>
     </div>

<?php
  }
  
  public function displayList(){
  	$workers = $this->_getSumWorkers();
  
   ?>
      <tr <?php if ($workers >= $this->num_workers) print 'class="barshift-full"'; ?>>
        <td><a href="index.php?page=display-barshift&amp;barshiftid=<?php print $this->id; ?>"><?php print $this->title; ?></a></td>
        <td><?php print $this->location_name; ?></td>
        <td><?php print formatDate($this->date, "td"); ?></td>				 
        <td><?php print formatTime($this->start); ?></td>
        <td><?php print formatTime($this->end); ?></td>
        <td class="worker-status"><?php print $workers."/".$this->num_workers; ?></td>
        <?php if(checkAuth("view-edit-options-barshift")){
        		displayOptionsMenuTable($this->id, BARSHIFT, "barshift", "view-edit-options-barshift");
        } ?>
        
      </tr>
<?php
  }  
  
  public function displayCalendar($year, $month){
  	$workers = $this->_getSumWorkers();
  	if ($this->barShiftIsFull()){
  		$full = "full";  		
  	}else {
  		$full = "not-full";
  	}
   ?>
   
   	<div class="calendar-barshift <?php print $full; ?>">
      <h4><?php print $this->start." - ".$this->end." (".$this->_getSumWorkers()."/".$this->num_workers.")";?></h4>
      <h4 class="calendar-title"><a href="index.php?page=display-barshift&amp;barshiftid=<?php print $this->id; ?>">
        <?php print $this->title." - ".$this->location_name; ?>
      </a>
      <!--<?php displayOptionsMenuCalendar($this->id, BARSHIFT, "barshift", "view-edit-options-barshift", "$year$month"); ?>-->
      </h4>
      <?php
        $bsw = new BarShiftWorkers();
  			$bsw->displayCalendarListByBarshift($this->id);

       	if (!$this->_getIsUserWorking(getCurrentUser())){?>
       	<a class="calendar-register-me" href="index.php?action=register-barshiftworker-self&amp;page=display-barshifts-calendar&amp;barshiftid=<?php print $this->id; ?>">
       		sett opp meg!
       	</a>
			<?php } ?>

    </div>
<?php
  }  

  public
  function _displayWorkerList(){
  	$bsw = new BarShiftWorkers();
  	$bsw->displayListByBarshift($this->id);
  }

  
  public
  function _getSumWorkers(){
  	if (!empty($this->sum_workers)) {
  		return $this->sum_workers;
  	}
  	$sql = "SELECT COUNT(*) AS workers " .
  				 "FROM din_barshiftworker " .
  				 "WHERE barshift_id = $this->id";
    
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){
        $this->sum_workers = $row->workers;
        return $row->workers;
      }
    }else {
      error($result->toString());
    }
  }

  public
  function _getIsUserWorking($user_id){
  	$sql = "SELECT user_id " .
  				 "FROM din_barshiftworker " .
  				 "WHERE barshift_id = $this->id " .
  				 "AND user_id = $user_id";
    
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
			if ($result->numRows() > 0){
				return true;
			}				
    }else {
      error($result->toString());
    }
 	  return false;				
  }
  
  public
  function barShiftIsFull(){
  	if ($this->_getSumWorkers() >= $this->num_workers) {
  		return true;
  	}
  	return false;
  }
}
?>