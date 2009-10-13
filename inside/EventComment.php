<?php


/*
 *This class requires PEAR::DB and functions library
 */
class EventComment {

  var $id;
  var $event_id;
  var $title;
  var $text;
  var $user_id_author;
  var $name_author;
  var $date;

  var $conn;

  function EventComment($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New eventComment
      if ($data == NULL){
        error("EventComment: No data supplied.");     
      }else {
        $this->user_id_author = getCurrentUser();
        $this->date   = NULL;

      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        $this->user_id_author     = $data['user_id_author'];
        $this->name_author = $data['name_author'];    
        $this->date       = substr($data['date'], 0, 16);
      }
    }
    //Common initializations
    $this->title     = $data['title'];
    $this->text      = $data['text'];
    $this->event_id  = $data['event_id'];    
  }

  public function store(){
    $this->conn->autoCommit(false);            
    if ($this->id == NULL){
      $sql = sprintf("INSERT INTO din_eventcomment 
                          (id, event_id, title, 
                           text, user_id_author, date)
                      VALUES 
                          (%s, %s, %s, %s, %s, NOW())", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->event_id),
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->user_id_author)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        $GLOBALS['extraScriptParams']['eventid'] = $this->event_id;
        if ($this->conn->commit()){
          notify("New eventComment registered and stored.");
        }else {
          $this->conn->rollback();
          error("New eventComment: " . $result->toString());
        }
      }else {
        $this->conn->rollback();
        error("New eventComment: " . $result->toString());
      }
    }else {
      $sql = sprintf("UPDATE din_eventcomment SET 
                        title     = %s,
                        text      = %s,
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        if ($this->conn->commit()){
          notify("EventComment updated.");
        }else {
          $this->conn->rollback();
          error("New eventComment: " . $result->toString());
        }
      }else {
        error("Update eventComment: " . $result->toString());
      }
    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT ec.*, CONCAT(u.firstname, ' ', u.lastname) AS name_author
            FROM din_eventcomment ec, din_user u
            WHERE ec.id = $this->id
            AND ec.user_id_author = u.id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("EventComments: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_eventcomment 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("EventComment deleted.");
    }else {
      error($result->toString());
    }
  }

  public function display(){
?>
    <div class="comment">
<?php
      displayOptionsMenu($this->id, EVENTCOMMENT, "eventcomment", "view-edit-options-eventcomment");
?>
      <div class="comment-meta">
        <h4>Lagt inn av:</h4>
        <p><?php print $this->name_author; ?></p>
        <p class="date"><?php print formatDatetimeShort($this->date); ?></p>				 
      </div>
      <div class="comment-main">
        <h3><?php print $this->title; ?></h3>
        <p><?php print prepareForHTML($this->text); ?></p>
      </div>
    </div>
<?php
  }
}

?>