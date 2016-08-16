<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Event {

  var $id;
  var $name;
  var $text;
  var $time;
  var $location;
  var $eventcategory_id;
  var $eventcategory_title;
  var $user_id_responsible;
  var $name_responsible;
  var $targetGroup;

  var $conn;

  function Event($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New event
      if ($data == NULL){
        error("Event: No data supplied."); 
      }else {
        //All relevant values are common
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        $this->eventcategory_title   = $data['eventcategory_title'];    
        $this->name_responsible = $data['name_responsible'];
      }
    }
    //Common initializations
    $this->name        = $data['name'];
    $this->text        = $data['text'];
    $this->time        = substr($data['time'], 0, 16);
    $this->location    = $data['location'];
    $this->eventcategory_id    = $data['eventcategory_id'];    
    $this->user_id_responsible = $data['user_id_responsible'];
    $this->targetGroup         = $data['targetGroup'];
  }

  public function store(){
    $this->conn->autoCommit(false);            
    if ($this->id == NULL){
      $this->id = getNextId("din_event");
      $sql = sprintf("INSERT INTO din_event 
                          (id, name, text, time, location, eventcategory_id, user_id_responsible, targetGroup)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->time),
                     $this->conn->quoteSmart($this->location),
                     $this->conn->quoteSmart($this->eventcategory_id),
                     $this->conn->quoteSmart($this->user_id_responsible),
                     $this->conn->quoteSmart($this->targetGroup)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $GLOBALS['extraScriptParams']['eventid'] = $this->id;
        $sql = sprintf("INSERT INTO din_eventupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart(scriptParam('eventid')),
                       $this->conn->quoteSmart(getCurrentUser()),                           
                       $this->conn->quoteSmart("Event registered.")
                      );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("New event registered and stored.");
          }else {
            $this->conn->rollback();
            error("New event: " . $result->toString());
          }
        }else {
            $this->conn->rollback();
            error("New event: " . $result->toString());
        }
      }else {
        $this->conn->rollback();
        error("New event: " . $result->toString());
      }
    }else {
      $sql = sprintf("UPDATE din_event SET 
                        name        = %s,
                        text        = %s,
                        time        = %s,
                        location    = %s,
                        eventcategory_id    = %s,
                        user_id_responsible = %s,
                        targetGroup         = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->time),
                     $this->conn->quoteSmart($this->location),
                     $this->conn->quoteSmart($this->eventcategory_id),
                     $this->conn->quoteSmart($this->user_id_responsible),
                     $this->conn->quoteSmart($this->targetGroup),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $sql = sprintf("INSERT INTO din_eventupdate 
                        VALUES (NULL, NOW(), %s, %s, %s)",
                       $this->conn->quoteSmart($this->id),
                       $this->conn->quoteSmart(getCurrentUser()),                           
                       $this->conn->quoteSmart("Event updated.")
                       );
        $result = $this->conn->query($sql);
        if (DB::isError($result) != true){
          if ($this->conn->commit()){
            notify("Event updated.");
          }else {
            $this->conn->rollback();
            error("New event: " . $result->toString());
          }
        }else {
          error("Update event: " . $result->toString());
        }        
      }else {
          error("Update event: " . $result->toString());
      }
    }
  }

  public function repeat($data){
    $frequency = $data['frequency'];
    $count = $data['count'];
    for ($i = 0; $i < $count; $i++){
      $date = get_repeat_date($this->time, $i + 1, $frequency);
      $sql = sprintf("INSERT INTO din_event
                          (id, name, text, time, location, eventcategory_id, user_id_responsible, targetGroup)
                      VALUES 
                          (NULL, %s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($date),
                     $this->conn->quoteSmart($this->location),
                     $this->conn->quoteSmart($this->eventcategory_id),
                     $this->conn->quoteSmart($this->user_id_responsible),
                     $this->conn->quoteSmart($this->targetGroup)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Event repeated.");
      }else {
        error("New event: " . $result->toString());
      }
    }
  }

  public function copy($data){
    $id = getNextId("din_event");
    $sql = sprintf("INSERT INTO din_event
                        (id, name, text, time, location, eventcategory_id, user_id_responsible, targetGroup)
                    VALUES 
                        (%s, %s, %s, %s, %s, %s, %s, %s)", 
                  $this->conn->quoteSmart($id),
                  $this->conn->quoteSmart($this->name),
                   $this->conn->quoteSmart($this->text),
                   $this->conn->quoteSmart($data['newDate']),
                   $this->conn->quoteSmart($this->location),
                   $this->conn->quoteSmart($this->eventcategory_id),
                   $this->conn->quoteSmart($this->user_id_responsible),
                   $this->conn->quoteSmart($this->targetGroup)
                   );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      notify("Aktivitet kopiert.");
      $GLOBALS['extraScriptParams']['eventid'] = $id;
    }else {
      notify("Problemer med kopiering av aktivitet.");
      error("New event: " . $result->toString());
    }
  }
  

  public function _retrieveData(){
    $sql = "SELECT e.*, ec.title AS eventcategory_title, 
                CONCAT(u.firstName, ' ', u.lastName) AS name_responsible
            FROM din_eventcategory ec, din_event e LEFT JOIN din_user u
            ON e.user_id_responsible = u.id
            WHERE e.id = $this->id
            AND e.eventcategory_id = ec.id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Events: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_event 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Event deleted.");
    }else {
      error($result->toString());
    }
  }

  public function display(){
    $this->_markAsRead();
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, EVENT, "event", "view-edit-options-event");
?>
    <h3><?php print $this->name; ?></h3>
    <p class="date"><?php print formatDatetime($this->time); ?></p>				 

    <p><?php print prepareForHTML($this->text); ?></p>

    <h4>Deltakere:</h4>
    <p><?php print prepareForHTML($this->targetGroup); ?></p>
    <h4>Arrangementsansvarlig:</h4>
    <p><?php print prepareForHTML($this->name_responsible); ?></p>
    <h4>Sted:</h4>
    <p><?php print $this->location; ?></p>
	<?php
    if (checkAuth("view-register-event")){
    
      $freqOptions = Array(Array("id" => "daily", "title" => "daglig"), 
                           Array("id" => "weekly", "title" => "ukentlig"),
                           Array("id" => "biweekly", "title" => "annenhver uke"),
                           Array("id" => "monthlyDate", "title" => "månedlig"),
                           Array("id" => "annual", "title" => "årlig")
                               );
                           
      $title   = "repeat event";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=repeat-event&amp;page=display-events";
      $fields  = Array(Array("label" => "eventid", "type" => "hidden",
                             "attributes" => Array("name" => "eventid", "value" => "$this->id")),
                       Array("label" => "gjenta aktivitet", "type" => "select", 
                             "attributes" => Array("name" => "frequency", "values" => $freqOptions,
                                                   "value" => $freqOptions[0])),
                       Array("label" => "antall ganger", "type" => "text",
                             "attributes" => Array("name" => "count", "size" => 3, "maxlength" => 3,
                                                   "value" => 0))
                       );
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");


      $title   = "copy event";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=copy-event&amp;page=display-event";
      $fields  = Array(Array("label" => "eventid", "type" => "hidden",
                             "attributes" => Array("name" => "eventid", "value" => "$this->id")),
                       Array("label" => "kopier til", "type" => "datetime", 
                             "attributes" => Array("name" => "newDate", "value" => "$this->time"))
                       );
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");
    }

    //display comments
    $comments = new EventComments();
    $list = $comments->getList($this->id);
    if ($list->numRows() > 0){
      ?>
    <div class="comments">
      <h3>kommentarer og spørsmål:</h3>
<?php
      while ($row =& $list->fetchRow(DB_FETCHMODE_OBJECT)){
        $comment = new EventComment($row->id);
        $comment->display();
      }?>
    </div>
<?php    }
    ?>
<?php
    //register new comment
    $title   = "legg inn kommentar eller spørsmål";
    $enctype = NULL;
    $method  = "post";
    $action  = "index.php?action=register-eventcomment&amp;page=display-event";
    $fields  = Array(Array("label" => "eventid", "type" => "hidden",
                           "attributes" => Array("name" => "event_id", "value" => "$this->id")),
                     Array("label" => "tittel", "type" => "text", 
                           "attributes" => Array("name" => "title", "size" => 50, "maxlength" => 50)),
                     Array("label" => "tekst", "type" => "textarea", 
                           "attributes" => Array("name" => "text", "cols" => 70, "rows" => 6))
                     );
    $form = new Form($title, $enctype, $method, $action, $fields);
    $form->display();


     ?>
     </div>

<?php
  }

  public function displayList(){
    $read = $this->_readByCurrentUser();
   ?>
      <tr>
        <td class="<?php ($read == true) ? print('read'): print('unread'); ?>">
          <?php ($read == true) ? print(''): print('*'); ?></td>
        <td><a class="<?php ($read == true) ? print('read'): print('unread'); ?>" href="index.php?page=display-event&amp;eventid=<?php print $this->id; ?>"><?php print $this->name; ?></a></td>
        <td><?php print $this->eventcategory_title; ?></td>
        <td><?php print prepareForHTML($this->name_responsible); ?></td>
        <td><?php print formatDatetime($this->time, "td"); ?></td>				 
        <?php displayOptionsMenuTable($this->id, EVENT, "event", "view-edit-options-event"); ?>
      </tr>
    
<?php
  }  

  public function _markAsRead(){
    if ($this->_readByCurrentUser() == true){
      return;
    }

    $user = getCurrentUser();
    if ($user == false){
      return;
    }
           
    $sql = sprintf("INSERT INTO din_eventread VALUES
                    (%s, %s, NOW())",
                   $this->conn->quoteSmart($this->id),
                   $this->conn->quoteSmart($user)
                   );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      //notify("Event marked as read.");
    }else {
      error($result->toString());
    }
  }

  public function _readByCurrentUser(){
    $user = getCurrentUser();
    if ($user == false){
      return false;
    }

    $sql = sprintf("SELECT event_id FROM din_eventread 
                    WHERE event_id = %s
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