<?php


/*
 *This class requires PEAR::DB and functions library
 */
class UserGroupRelationship {

  var $user_id;
  var $user_name;
  var $group_id;
  var $group_name;

  var $conn;

  function UserGroupRelationship($user_id = NULL, $group_id = NULL) {
    $this->__construct($user_id, $group_id);
  }

  public
  function __construct($user_id = NULL, $group_id = NULL) {

    $conn = & DB :: connect(getDSN());
    if (DB :: isError($conn)) {
      print ("error: ".$conn->toString());
      exit ();
    } else {
      $this->conn = $conn;
    }

    $this->user_id = $user_id;
    $this->group_id = $group_id;
    $action = scriptParam("action");

    $data = $this->_retrieveData();
    $this->user_name = $data['username'];
    $this->group_name = $data['name'];
  }

  public
  function store() {
    $sql = sprintf("INSERT INTO din_usergrouprelationship "."  (user_id, group_id) "."VALUES "."  (%s, %s)", $this->conn->quoteSmart($this->user_id), $this->conn->quoteSmart($this->group_id));
    $result = $this->conn->query($sql);
    if (DB :: isError($result) != true) {
      $user = new User($this->user_id);
      $group = new Group($this->group_id);
      notify("Bruker <strong>$user->firstname $user->lastname</strong>"." registrert i gruppen <strong>$group->name</strong>.");
      $this->_checkActive();
      $this->_checkMod();
      subscribe_mailinglist($this->user_id, $this->group_id);
    } else
      if ($result->getCode() == -5) {
        notify("<strong>$this->user_name</strong> er allerede medlem i gruppen <strong>$this->group_name</strong>.");
      } else {
        notify("Problem med registrering av gruppemedlemskap.");
        error("Register user in group: ".$result->toString());
      }
  }

  public
  function _retrieveData() {
    $sql = "SELECT CONCAT(u.firstname, ' ', u.lastname) AS username, g.name
                            FROM din_usergrouprelationship ugr, din_user u, din_group g
                            WHERE ugr.user_id = $this->user_id
                            AND ugr.group_id = $this->group_id
                            AND ugr.user_id = u.id
                            AND ugr.group_id = g.id";
    $result = & $this->conn->query($sql);
    if (DB :: isError($result) != true) {
      if ($row = & $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        return $row;
      }
    } else {
      error("UserGroupRelationships: ".$result->toString());
    }
  }

  public
  function delete($user_id, $group_id) {
    $conn = db_connect();
    $sql = "DELETE FROM din_usergrouprelationship 
                            WHERE user_id = $user_id 
                            AND group_id = $group_id
                            LIMIT 1";
    $result = $conn->query($sql);
    if (DB :: isError($result) != true) {
      notify("Gruppemedlemskap slettet.");
      unsubscribe_mailinglist($user_id, $group_id);

      //Update moderator status
      $group = new Group($group_id);
      if ($group->admin == 1) {
        $sql = "SELECT * "."FROM din_group g, din_usergrouprelationship ugr "."WHERE ugr.user_id = $user_id "."AND ugr.group_id = g.id "."AND g.admin = 1";
        $result = $conn->query($sql);
        if (DB :: isError($result) != true) {
          if ($result->numRows() == 0) {
            UserGroupRelationship :: delete($user_id, 61);
          }
        } else {
          error("UserGroupRelationships - admin: ".$result->toString());
        }
      }
    } else {
      error($result->toString());
    }
  }

  public
  function displayList() {
?>
      <tr>
        <td><a href="index.php?page=display-user&amp;userid=<?php print $this->user_id; ?>"><?php print $this->user_name; ?></a></td>
        <td><?php print "$this->group_name"; ?></td>
        <?php


    if ($this->group_id != 1) {
      displayOptionsMenuTable(Array("user_id" => $this->user_id, "group_id" => $this->group_id), USERGROUPRELATIONSHIP, "usergrouprelationship", "view-edit-options-usergrouprelationship", false);
    }
?>
      </tr>
    
<?php


  }

  public
  function _checkActive() {
    if ($this->group_id > 2) {
      $sql = "SELECT * "."FROM din_usergrouprelationship ugr "."WHERE ugr.user_id = $this->user_id "."AND ugr.group_id = 2";
      $result = $this->conn->query($sql);
      if (DB :: isError($result) != true) {
        if ($result->numRows() == 0) {
          $newRel = new UserGroupRelationship($this->user_id, 2);
          $newRel->store();
        }
      } else {
        error("UserGroupRelationships - admin: ".$result->toString());
      }
    }
  }

  public
  function _checkMod() {
    $group = new Group($this->group_id);
    if ($group->admin == 1) {
      $sql = "SELECT * "."FROM din_usergrouprelationship ugr "."WHERE ugr.user_id = $this->user_id "."AND ugr.group_id = 61";
      $result = $this->conn->query($sql);
      if (DB :: isError($result) != true) {
        if ($result->numRows() == 0) {
          $newRel = new UserGroupRelationship($this->user_id, 61);
          $newRel->store();
        }
      } else {
        error("UserGroupRelationships - admin: ".$result->toString());
      }
    }
  }


}
?>