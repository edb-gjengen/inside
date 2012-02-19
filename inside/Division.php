<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Division {

  var $id;
  var $name;
  var $nicename;
  var $text;
  var $phone;
  var $email;
  var $office;
  var $user_id_contact;
  var $name_contact;
  var $url;
  var $divisioncategory_id;
  var $divisioncategory_title;
  var $picture;
  var $conn;


  function Division($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){

    if (!function_exists('_sanitizeForUrls')) :
    /**
     * Sanitize username stripping out unsafe characters.
     *
     * If $strict is true, only alphanumeric characters (as well as _, space, ., -,
     * @) are returned.
     * Removes tags, octets, entities, and if strict is enabled, will remove all
     * non-ASCII characters. After sanitizing, it passes the username, raw username
     * (the username in the parameter), and the strict parameter as parameters for
     * the filter.
     *
     * @param string $username The username to be sanitized.
     * @param bool $strict If set limits $username to specific characters. Default false.
     * @return string The sanitized username, after passing through filters.
     */
    function _sanitizeForUrls( $username, $strict = false ) {
        //$username = wp_strip_all_tags($username);

        $username = strtolower($username);
        $username = strtr($username, "äåöæøå", "aaoaoa");

        // Kill octets
        $username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
        $username = preg_replace('/&.+?;/', '', $username); // Kill entities

        // If strict, reduce to ASCII for max portability.
        if ( $strict )
            $username = preg_replace('|[^a-z0-9 _.\-@]|i', '', $username);

        // Consolidate contiguous whitespace
        $username = preg_replace('|\s+|', ' ', $username);

        $username = str_replace('.', '-', $username);
        $username = preg_replace('/[^%a-z0-9 _-]/', '', $username);
        $username = preg_replace('/\s+/', '-', $username);
        $username = preg_replace('|-+|', '-', $username);
        $username = trim($username, '-');

        return $username;
    }
    endif;

    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New division
      if ($data == NULL){
        error("Division: No data supplied.");     
      }else {
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article
        if($_FILES['userfile']['error'] != 4) {
          $temp_name = new_file($_FILES['userfile'], "foreninger");
          rename_file($temp_name, $this->id, "foreninger");
          $ext = strtolower( pathinfo($temp_name, PATHINFO_EXTENSION) );
          $this->picture = $this->id . "." . $ext;
        }else {
          $this->picture = "picture"; 
        }
      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        $this->name_contact           = $data['name_contact'];    
        $this->divisioncategory_title = $data['divisioncategory_title'];    
        $this->picture                = $data['picture'];
      }
    }
    //Common initializations
    $this->name                = $data['name'];
    $this->nicename            = _sanitizeForUrls( $this->name );
    $this->text                = $data['text'];
    $this->phone               = $data['phone'];
    $this->email               = $data['email'];    
    $this->office              = $data['office'];
    $this->user_id_contact     = $data['user_id_contact'];
    $this->url                 = $data['url'];    
    $this->divisioncategory_id = $data['divisioncategory_id'];
    $this->updated	       = $data['updated'];
  }

  public function store(){
    if ($this->id == NULL){
      $sql = sprintf("INSERT INTO din_division 
                          (id, name, nicename, text, phone, email, office, 
                           user_id_contact, url, divisioncategory_id)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart(_sanitizeForUrls($this->name),true),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->phone),
                     $this->conn->quoteSmart($this->email),
                     $this->conn->quoteSmart($this->office),
                     $this->conn->quoteSmart($this->user_id_contact),
                     $this->conn->quoteSmart($this->url),
                     $this->conn->quoteSmart($this->divisioncategory_id)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("New division registered and stored.");
      }else {
        error("New division: " . $result->toString());
      }
      
    }else {
      $sql = sprintf("UPDATE din_division SET 
                        name                = %s,
                        text                = %s,
                        phone               = %s,
                        email               = %s,
                        office              = %s,
                        user_id_contact     = %s,
                        url                 = %s,
                        divisioncategory_id = %s, " .
                       "picture             = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->phone),
                     $this->conn->quoteSmart($this->email),
                     $this->conn->quoteSmart($this->office),
                     $this->conn->quoteSmart($this->user_id_contact),
                     $this->conn->quoteSmart($this->url),
                     $this->conn->quoteSmart($this->divisioncategory_id),
                     $this->picture,
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Forening oppdatert.");
      }else {
        error("Update division: " . $result->toString());
      }

    }
  }
  
  public function _retrieveData(){
    $sql = "SELECT d.*, CONCAT(u.firstname, ' ', u.lastname) AS name_contact,
              dc.title AS divisioncategory_title
            FROM din_division d, din_user u, din_divisioncategory dc
            WHERE d.id = $this->id
            AND d.user_id_contact = u.id
            AND d.divisioncategory_id = dc.id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
				$row['text'] = stripslashes($row['text']);      
        return $row;
      }
    }else {
      error("Division: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_division 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      if ($conn->affectedRows() > 0){
        notify("Foreningen er slettet.");
      }else {
        notify("Ugyldig foreningsid, ingen handling utført.");        
      }
    }else {
      if ($result->getCode() == -3){
        notify("Det er brukergrupper knyttet til denne foreningen. Disse må slettes først.");
      }else {
        error($result->toString());
      }
    }
  }
  
  public
  function getNoAdminGroup(){
    $sql = "SELECT d.id AS group_id FROM din_group d, din_division dd " .
           "WHERE d.division_id=dd.id " .
           "AND dd.id = $this->id " .
           "AND d.admin = 0 " .
           "LIMIT 1";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)){
        return $row->group_id; 
      }else {
       return false; 
      }
    }else {
      error("no-admin-group: " . $result->toString());
      return false;
    }
  }

  public function display(){
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, DIVISION, "division", "view-edit-options-division");

    if ($this->picture != 0){
    print("<img src=\"imageResize.php?pic=http://www.studentersamfundet.no/bilder/foreninger/".$this->picture."&amp;maxwidth=200\" alt=\"\" />");
  }

?>
      <h3><?php print $this->name; ?></h3>
      <p class="subtitle"><?php print $this->divisioncategory_title; ?></p>
      <p><?php print prepareForHTML($this->text); ?></p>
      <p><strong>Telefon:</strong> <?php print formatPhone($this->phone); ?></p>
      <p><strong>Epost:</strong> <?php print $this->email; ?></p>
      <p><strong>Hjemmeside:</strong> <?php print $this->url; ?></p>
      <p><strong>Kontornummer:</strong> <?php print $this->office; ?></p>
      <p><strong>Kontaktperson:</strong> 
      	<a href="index.php?section=users&amp;page=display-user&amp;userid=<?php print $this->user_id_contact; ?>"><?php print $this->name_contact; ?></a>
      </p>

     </div>
     
<?php
  }

  public function displayList(){
   ?>
      <tr>
        <td><a href="index.php?page=display-division&amp;divisionid=<?php print $this->id; ?>"><?php print $this->name; ?></a></td>
        <td><?php print $this->phone; ?></td>
        <td><?php print $this->email; ?></td>
        <td><a class="contact" href="index.php?page=display-user&amp;userid=<?php print $this->user_id_contact; ?>"
             title="mer informasjon om <?php print $this->name_contact; ?>"
             ><?php print $this->name_contact; ?>
          </a></td>				 
        <?php displayOptionsMenuTable($this->id, DIVISION, "division", "view-edit-options-division"); ?>
      </tr>
    
<?php
  }  

}

?>
