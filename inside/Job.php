<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Job {

  var $id;
  var $name;
  var $text;
  var $jobcategory_id;
  var $jobcategory_title;
  var $contactInfo;
  var $registered;
  var $user_id_registered;
  var $published;
  var $expires;

  var $position_id;


  var $conn;

  function Job($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New job
      if ($data == NULL){
        error("Job: No data supplied.");     
      }else {
        $this->registered    = date("Y-m-d H:i:s");
        $this->user_id_registered  = getCurrentUser();
        $this->published     = date("Y-m-d H:i:s");
        $this->position_id        = (isset($data['positionid'])) ? $data['positionid'] : 0;
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        if ($data == NULL){
          notify("Stillingen du søkte er ikke tilgjengelig. Bruk skjemaet under om du vil rapportere problemet.");
          displayBugReportForm("job");
          $this->id = NULL;
        }
        $this->jobcategory_title  = $data['jobcategory_title'];    
        $this->registered         = $data['registered'];
        $this->user_id_registered = $data['user_id_registered'];
        $this->published          = $data['published'];
        $this->position_id        = (isset($data['position_id'])) ? $data['position_id'] : 0;
        
      }
    }
    //Common initializations
    $this->name           = stripcslashes($data['name']);
    $this->text           = stripcslashes($data['text']);
    $this->jobcategory_id = $data['jobcategory_id'];    
    $this->contactInfo    = $data['contactInfo'];
    $this->expires        = $data['expires'];
  }

  public function store(){
    $this->conn->autoCommit(false);            
    if ($this->id == NULL){
    	$this->id = getNextId("din_job");
      $sql = sprintf("INSERT INTO din_job 
                          (id, name, text, jobcategory_id, contactInfo, 
                           registered, user_id_registered, published, 
                           expires, position_id)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->jobcategory_id),
                     $this->conn->quoteSmart($this->contactInfo),
                     $this->conn->quoteSmart($this->registered),
                     $this->conn->quoteSmart($this->user_id_registered),
                     $this->conn->quoteSmart($this->published),
                     $this->conn->quoteSmart($this->expires),
                     $this->conn->quoteSmart($this->position_id)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $GLOBALS['extraScriptParams']['jobid'] = $this->id;

        $sql = sprintf("INSERT INTO din_jobupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart($this->id),
                       $this->conn->quoteSmart(getCurrentUser()),
                       $this->conn->quoteSmart("Job registered.")
                       );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("New job registered and stored.");
          }else {
            $this->conn->rollback();
            error("New jobb: " . $result->toString());
          }
        }else {
          $this->conn->rollback();
          error("New job: " . $result->toString());
        }
      }else {
        error("New job: " . $result->toString());
      }
    
    }else {
      $sql = sprintf("UPDATE din_job SET 
                        name           = %s,
                        text           = %s,
                        jobcategory_id = %s,
                        contactInfo    = %s,
                        expires        = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->jobcategory_id),
                     $this->conn->quoteSmart($this->contactInfo),
                     $this->conn->quoteSmart($this->expires),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $sql = sprintf("INSERT INTO din_jobupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart($this->id),
                       $this->conn->quoteSmart(getCurrentUser()),                           
                       $this->conn->quoteSmart("Job updated.")
                       );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("Job updated.");
          }else {
            $this->conn->rollback();
            error("Update job: " . $result->toString());
          }
        }else {
            $this->conn->rollback();
          error("Update job: " . $result->toString());
        }
      }else {
        $this->conn->rollback();
        error("Update job: " . $result->toString());
      }
    }
  }

  public function _retrieveData(){
    $sql = "SELECT j.*, jc.title AS jobcategory_title
            FROM din_job j, din_jobcategory jc
            WHERE j.id = $this->id
            AND j.jobcategory_id = jc.id";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Jobs: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_job 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Job deleted.");
    }else {
      error($result->toString());
    }
  }

  public function display(){
    if ($this->id == NULL){
      return false;
    }
    $this->_markAsRead();
?>
    <div class="text-column">
    <div class="timeData">
      <strong>Publisert:</strong> <?php print formatDate($this->published); ?> 
      <strong>Utgår:</strong> <?php print formatDate($this->expires); ?>
    </div>

<?php
   displayOptionsMenu($this->id, JOB, "job", "view-edit-options-job");
?>
    <h3><?php print $this->name; ?></h3>

    <p><?php print prepareForHTML($this->text); ?></p>
    <h4>kontaktinfo:</h4>
    <p><?php print prepareForHTML($this->contactInfo); ?></p>
    </div>
    
<?php
  }

  public function displayList(){
    $read = $this->_readByCurrentUser();
    $division = $this->_getLinkedToPosDivName();
   ?>
      <tr>
        <td class="<?php ($read == true) ? print('read'): print('unread'); ?>">
          <?php ($read == true) ? print(''): print('*'); ?></td>
        <td><a class="<?php ($read == true) ? print('read'): print('unread'); ?>" href="index.php?page=display-job&amp;jobid=<?php print $this->id; ?>"><?php print $this->name; ?></a></td>
        <td><?php print $this->jobcategory_title; ?></td>
        <td><?php print $division; ?></td>
        <td class="date"><?php print formatDatetime($this->published, "td"); ?></td>				 
        <td class="date"><?php print formatDatetime($this->expires, "td"); ?></td>				 
        <?php displayOptionsMenuTable($this->id, JOB, "job", "view-edit-options-job"); ?>
      </tr>
    
<?php
  }
  
  public function _getLinkedToPosDivName(){
    if ($this->position_id > 0){
      $sql = "SELECT d.name 
              FROM din_position p, din_division d
              WHERE $this->position_id = p.id
              AND p.division_id = d.id";
      $result = $this->conn->query($sql);
      if (DB::isError($result) == true){
        notify("Job: " . $result->toString());
        return "utilgjengelig";
      }else {
        if ($row =& $result->fetchRow(DB_FETCHMODE_ORDERED)){
          return $row[0];
        }else {
          return "utilgjengelig";
        }
      }
    }else {
      return "uspesifisert";
    }
  }

  public function _markAsRead(){
    if ($this->_readByCurrentUser() == true){
      return;
    }

    $user = getCurrentUser();
    if ($user == false){
      return;
    }
           
    $sql = sprintf("INSERT INTO din_jobread VALUES
                    (%s, %s, NOW())",
                   $this->conn->quoteSmart($this->id),
                   $this->conn->quoteSmart($user)
                   );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      //notify("Job marked as read.");
    }else {
      error($result->toString());
    }
  }

  public function _readByCurrentUser(){
    $user = getCurrentUser();
    if ($user == false){
      return false;
    }

    $sql = sprintf("SELECT job_id FROM din_jobread 
                    WHERE job_id = %s
                    AND user_id = %s",
                   $this->conn->quoteSmart($this->id),
                   $this->conn->quoteSmart($user)
                   );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      if($result->numRows() > 0){
        return true;
      }
    }else {
      error($result->toString());
    }
    return false;
  }
  

}

?>