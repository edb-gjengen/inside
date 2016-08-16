<?php

class BugReport {
  var $id;
  var $title;
  var $comment;
  var $date;
  var $type;
  var $filename;
  var $query;
  var $referer;
  var $useragent;
  var $user_id;
  var $active;
  var $status_comment;
  
  function BugReport($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();
    $this->id = $id;

    if ($id == NULL){//New job
      if ($data == NULL){
        error("Bugreport: No data supplied.");     
      }else {

      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        $this->date           = $data['date'];
        $this->active         = $data['active'];
        $this->status_comment = stripcslashes($data['status_comment']);
      }
    }
    //Common initializations
    $this->title     = stripcslashes($data['title']);
    $this->comment   = $data['comment'];
    $this->type      = $data['type'];
    $this->filename  = $data['filename'];
    $this->query     = $data['query'];
    $this->referer   = $data['referer'];
    $this->useragent = $data['useragent'];
    $this->user_id   = $data['user_id'];
  }

  public function store(){
    $this->conn->autoCommit(false);            
    if ($this->id == NULL){
      $this->id = getNextId("din_bugreport");
      $sql = sprintf("INSERT INTO din_bugreport VALUES
                      (%s, NOW(), %s, %s, %s, %s, %s, %s, %s, %s)",
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->type),
                     $this->conn->quoteSmart($this->filename),
                     $this->conn->quoteSmart($this->query),
                     $this->conn->quoteSmart($this->referer),
                     $this->conn->quoteSmart($this->useragent),
                     $this->conn->quoteSmart($this->user_id),
                     $this->conn->quoteSmart($this->comment),
                     $this->conn->quoteSmart($this->title)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $GLOBALS['extraScriptParams']['bugreportid'] = $this->id;
        if ($this->conn->commit()){
          notify("Feilmelding er registrert.");
        }else {
          $this->conn->rollback();
          error("New bug report: " . $result->toString());
        }
      }else {
        $this->conn->rollback();
        error("New bug report: " . $result->toString());
      }
    } else {
      $sql = sprintf("UPDATE din_bugreport SET 
                        title   = %s,
                        comment = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->comment),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        if ($this->conn->commit()){
          notify("Feilmelding oppdatert.");
        }else {
          $this->conn->rollback();
          error("Update bug report: " . $result->toString());
        }
      }else {
        error("Update bug report: " . $result->toString());
      }        
    }
  }

	public
	function setStatus($data) {
		$sql = sprintf("UPDATE din_bugreport SET 
                      active         = %s,
                      status_comment = %s
                    WHERE 
                      id = %s",
                   $this->conn->quoteSmart($data['active']),
                   $this->conn->quoteSmart($data['status_comment']),
                   $this->conn->quoteSmart($this->id)
                   );
    $result =& $this->conn->query($sql);

    if (DB::isError($result) == true){
      error("Bug report-status: " . $result->toString());
    }
	}

  public function _retrieveData(){
    $sql = "SELECT *
            FROM din_bugreport dc
            WHERE id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Bug report: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_bugreport 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Feilmelding slettet.");
    }else {
      error($result->toString());
    }
  }

  public 
  function displayList(){
    $user = new User($this->user_id);
    ?>
      <tr id="bugreport<?php print $this->id; ?>">
        <td><a href="index.php?page=display-bugreport&amp;bugreportid=<?php print $this->id; ?>"><?php print $this->title; ?></a></td>
        <td><?php print prepareForHTML($this->comment); ?></td>
        <td><?php print "$user->firstname $user->lastname"; ?></td>
      </tr>
<?php
  }  

  public 
  function display(){
   ?>
    <div class="text-column">
<?php     displayOptionsMenu($this->id, BUGREPORT, "bugreport", "view-edit-options-bugreport", false);
 ?>
    <h3>Feilmelding: <?php print $this->title; ?></h3> 
    <h4>Kommentar:</h4>
    <p><?php print prepareForHTML($this->comment); ?></p>
    <p><strong>Registrert:</strong> <?php print $this->date; ?></p>
    <p><strong>Fil:</strong> <?php print $this->filename; ?></p>
    <p><strong>Query:</strong> <?php print $this->query ;?></p>
    <p><strong>URL:</strong> <?php print $this->referer ;?></p>
    <p><strong>Nettleser:</strong> <?php print $this->useragent ;?></p>
    <?php 
    $user = new User($this->user_id);
    ?>
    <p><strong>Bruker:</strong> <a href="index.php?page=display-user&amp;userid=<?php print $this->user_id; ?>"
             title="mer informasjon om <?php print $user->firstname.' '.$user->lastname; ?>">
        <?php print "$user->firstname $user->lastname";?></p></a>
		
		<?php
  	 $statuses = Array(Array("id" => 0, "title" => "avsluttet"),
                			 Array("id" => 1, "title" => "åpen")
                			);
  	 
  	 
  	  $title   = "status på feilmeldingen";
    	$id      = "bugreport-comment";
    	$enctype = NULL;
    	$method  = "post";
    	$action  = "index.php?action=update-bugreport-status&amp;page=display-bugreport";
    	$fields  = Array();
    
	    $fields[] = Array("label" => "bugreportid", "type" => "hidden",
                            "attributes" => Array("name" => "bugreportid", "value" => $this->id));
			
			$fields[] = Array("label" => "status", "type" => "select",
			                   "attributes" => Array("name" => "active", "values" => $statuses,
      		                                     "currentValue" => $this->active));			
    	
    	$fields[] = Array("label" => "kommentar", "type" => "textarea", 
      	                "attributes" => Array("name" => "status_comment",
      	                											"value" => $this->status_comment,
      	                											"rows" => 7, "cols" => 50));
    	
    	$form = new Form($title, $enctype, $method, $action, $fields, $id);
    	$form->display();
		?>
  </div>
<?php
  }  

}
?>