<?php

/*
 *This class requires PEAR::DB and functions library
 */
class Concert {

  var $id;
  var $name;
  var $intro;
  var $text;
  var $name_en;
  var $intro_en;
  var $text_en;
  var $concertcategory_id;
  var $host_id;
  var $host_name;
  var $user_id_responsible;
  var $user_name_responsible;
  var $user_phone_responsible;
  var $user_email_responsible;
  var $time;
  var $venue_id;
  var $venue_name;
  var $comment;
  var $priceNormal;
  var $priceConcession;
  var $picture;
  var $links;
  var $ticketLink;
  var $facebookLink;
  var $user_name_ext_contact;
  var $user_phone_ext_contact;
  var $user_email_ext_contact;
  var $user_role_ext_contact;
  var $needPosters;
  var $viewWeekprogram;

  var $conn;

  function Concert( $id = NULL, $data = NULL ) {
    $this->__construct( $id, $data );
  }

  /**
   * Creates a Concert object.
   *
   * If ID is not set, the constructor will create a brand new Concert. If both
   * the ID and data is set, the constructor will update a concert that
   * already exists in the database. If only the ID is set, the constructor
   * will fetch a Concert from the database and prepare it for being displayed.
   */
  public function __construct( $id = NULL, $data = NULL ) {
    $this->conn = db_connect("dns");

    $this->id = $id;

    if ( $id == NULL ) { // New concert
      if ( $data == NULL ) {
        notify("Concert: No data supplied.");     
      } else {
        if ( isset ($data['user_id_responsible']) ) {
            $this->user_id_responsible   = $data['user_id_responsible'];
            
            if ( $user = new User($this->user_id_responsible) ) {
                $this->user_name_responsible  = $user->firstname . " " . $user->lastname;
                $this->user_phone_responsible = $user->phonenumber;
                $this->user_email_responsible = $user->email;
            }
        }
        $this->links = $data['links'];
      }
    } else { // ID set, existing concert
      if ( $data != NULL ) { // UPDATE
        // First, pick the image from the database
        $this->picture = $data['picture']; // @TODO $data['picture'] doesn't seem to be set! 

	var_dump($this->picture);

        // If the user uploaded a new image, store it and pick this image instead
        if( $_FILES['userfile']['error'] != 4 ) {
          $temp_name = new_file($_FILES['userfile'], "program");
          rename_file($temp_name, $this->id, "program");
	  $this->picture = $this->id.substr($temp_name, -4);
        }

        $this->links = $data['links'];
      } else { // Retrieve
        $data = $this->_retrieveData();
        $this->venue_name = $data['venue_name'];
        $this->host_name  = $data['host_name'];
        $this->picture    = $data['picture'];
      }
    }

    // Common initializations
    $this->name               = stripslashes($data['name']);
    $this->intro              = stripslashes($data['intro']);
    $this->text               = stripslashes($data['text']);
    $this->name_en            = stripslashes($data['name_en']);
    $this->intro_en           = stripslashes($data['intro_en']);
    $this->text_en            = stripslashes($data['text_en']);
    $this->host_id            = $data['host_id'];
    $this->time               = substr($data['time'], 0, 16);
    $this->concertcategory_id = $data['concertcategory_id'];
    $this->venue_id           = $data['venue_id'];
    $this->comment            = stripslashes($data['comment']);
    $this->priceNormal        = $data['priceNormal'];
    $this->priceConcession    = $data['priceConcession'];
    $this->ticketLink         = $data['ticketLink'];
    $this->facebookLink       = $data['facebookLink'];
    $this->user_name_ext_contact  = $data['user_name_ext_contact'];
    $this->user_phone_ext_contact = $data['user_phone_ext_contact'];
    $this->user_email_ext_contact = $data['user_email_ext_contact'];
    $this->user_role_ext_contact  = $data['user_role_ext_contact'];

    if (isset($data['viewWeekprogram'])) { 
        $this->viewWeekprogram = $data['viewWeekprogram'];
    } else {
        $this->viewWeekprogram = 1;
    }

    if (isset($data['needPosters'])) {
      $this->needPosters = ($data['needPosters'] == 'poster-true')? 'Ja' : 'Nei';
    } else {
      $this->needPosters = 'Nei';
    }
  } 

  /**
   * Stores a new concert to the database, or updates an existing concert.
   *
   * Called by _updateConcert() in ActionParser.php.
   */
  public function store(){
    if ($this->id == NULL) {
      // Storing a new concert

      if (!$this->_validate()){
        $GLOBALS['extraScriptParams']['page'] = "register-concert";
        return false; 
      }
      $this->id = getNextId("program");
      if($_FILES['userfile']['error'] != 4){
        $temp_name = new_file($_FILES['userfile'], "program");
        rename_file($temp_name, $this->id, "program");
        $this->picture = $this->id.substr($temp_name, -4);
      } else {
        $this->picture = "0"; 
      }
      $sql = sprintf("INSERT INTO program 
                          (id, tittel, tittel_en, ingress, ingress_en, type, arr, tid, tekst, tekst_en, kommentar, 
                           sted, vpris, mpris, billett, ansv, ansv_tlf, ansv_epost,
                           ekst_navn, ekst_tlf, ekst_epost, ekst_rolle, plakat_behov, vedlegg, linker, facebook, visUkeprogram)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
                           %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '%s')", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->name_en),
                     $this->conn->quoteSmart($this->intro),
                     $this->conn->quoteSmart($this->intro_en),
                     $this->conn->quoteSmart($this->concertcategory_id),
                     $this->conn->quoteSmart($this->host_id),
                     $this->conn->quoteSmart($this->time),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->text_en),
                     $this->conn->quoteSmart($this->comment),
                     $this->conn->quoteSmart($this->venue_id),
                     $this->conn->quoteSmart($this->priceNormal),
                     $this->conn->quoteSmart($this->priceConcession),
                     $this->conn->quoteSmart($this->ticketLink),
                     $this->conn->quoteSmart($this->user_name_responsible),
                     $this->conn->quoteSmart($this->user_phone_responsible),
                     $this->conn->quoteSmart($this->user_email_responsible),
                     $this->conn->quoteSmart($this->user_name_ext_contact),
                     $this->conn->quoteSmart($this->user_phone_ext_contact),
                     $this->conn->quoteSmart($this->user_email_ext_contact),
                     $this->conn->quoteSmart($this->user_role_ext_contact),
                     $this->conn->quoteSmart($this->needPosters),
                     $this->conn->quoteSmart($this->picture),
                     $this->conn->quoteSmart($this->links),
                     $this->conn->quoteSmart($this->facebookLink),
                     "1"
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Nytt arrangement er registrert.");
        $GLOBALS['extraScriptParams']['concertid'] = $this->id;
      }else {
        error("New concert: " . $result->toString());
        notify("Problemer med registrering av konsert.");
      }      
    } else {
      // Updating an existing concert

      if (!$this->_validate()){
        $GLOBALS['extraScriptParams']['page'] = "edit-concert";
        return false; 
      }
      $sql = sprintf("UPDATE program SET 
                        tittel        = %s,
                        tittel_en     = %s,
                        ingress       = %s,
                        ingress_en    = %s,
                        type          = %s,
                        arr           = %s,
                        tid           = %s,
                        tekst         = %s,
                        tekst_en      = %s,
                        kommentar     = %s,
                        sted          = %s,
                        vpris         = %s,
                        mpris         = %s,
                        billett       = %s,
                        facebook      = %s,
                        ansv          = %s,
                        ansv_tlf      = %s,
                        ansv_epost    = %s,
                        ekst_navn     = %s,
                        ekst_tlf      = %s,
                        ekst_epost    = %s,
                        ekst_rolle    = %s,
                        plakat_behov  = %s,
                        linker        = %s,
                        visUkeprogram = '%s'
                      WHERE 
                        id = %s;",
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->name_en),
                     $this->conn->quoteSmart($this->intro),
                     $this->conn->quoteSmart($this->intro_en),
                     $this->conn->quoteSmart($this->concertcategory_id),
                     $this->conn->quoteSmart($this->host_id),
                     $this->conn->quoteSmart($this->time),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->text_en),
                     $this->conn->quoteSmart($this->comment),
                     $this->conn->quoteSmart($this->venue_id),
                     $this->conn->quoteSmart($this->priceNormal),
                     $this->conn->quoteSmart($this->priceConcession),
                     $this->conn->quoteSmart($this->ticketLink),
                     $this->conn->quoteSmart($this->facebookLink),
                     $this->conn->quoteSmart($this->user_name_responsible),
                     $this->conn->quoteSmart($this->user_phone_responsible),
                     $this->conn->quoteSmart($this->user_email_responsible),
                     $this->conn->quoteSmart($this->user_name_ext_contact),
                     $this->conn->quoteSmart($this->user_phone_ext_contact),
                     $this->conn->quoteSmart($this->user_email_ext_contact),
                     $this->conn->quoteSmart($this->user_role_ext_contact),
                     $this->conn->quoteSmart($this->needPosters),
                     $this->conn->quoteSmart($this->links),
                     $this->viewWeekprogram,
                     $this->conn->quoteSmart($this->id)
	     );

      // If there's an image in this object, update the database
      if ( $this->vedlegg ) {
	      $sql .= "UPDATE program
		       SET vedlegg=" . $this->conn->quoteSmart($this->picture) . "
		       WHERE id=" . $this->id;
	      // @TODO Flush image cache for the old image, as the new one will have the same name
      }

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Arrangement oppdatert.");       
      }else {
        error("Update concert: " . $result->toString());
        notify("Problemer med oppdatering av arrangement.");
      }

    }
  }

  public
  function _validate(){
    $valid = true;
    if($this->name == '') { 
      notify("Tittel m&aring; angis.");
      $valid = false;
    }
    if($this->intro == '') { 
      notify("Ingress m&aring; angis.");
      $valid = false;
    }
    if (date("Y", strtotime($this->time)) < 2000){
      notify("Ugyldig &aring;rstall.");
      $valid = false;
    }
    if ($this->id == NULL && $this->picture == NULL && isset($_FILES['userfile']) && empty($_FILES['userfile']['name'])){
      notify("Bilde m&aring; velges");
      $valid = false;
    }
    return $valid;
  }

  public function repeat($data){
    $frequency = $data['frequency'];
    $count = $data['count'];
    for ($i = 0; $i < $count; $i++){
      $date = get_repeat_date($this->time, $i + 1, $frequency);
      $sql = sprintf("INSERT INTO program 
                          (id, tittel, tittel_en, ingress, ingress_en, type, arr, tid, tekst, tekst_en, kommentar, 
                           sted, vpris, mpris, billett, ansv, ansv_tlf, ansv_epost,
                           ekst_navn, ekst_tlf, ekst_epost, ekst_rolle, plakat_behov, vedlegg, linker)
                      VALUES 
                          (NULL, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
                           %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->name_en),
                     $this->conn->quoteSmart($this->intro),
                     $this->conn->quoteSmart($this->intro_en),
                     $this->conn->quoteSmart($this->concertcategory_id),
                     $this->conn->quoteSmart($this->host_id),
                     $this->conn->quoteSmart($date),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->text_en),
                     $this->conn->quoteSmart($this->comment),
                     $this->conn->quoteSmart($this->venue_id),
                     $this->conn->quoteSmart($this->priceNormal),
                     $this->conn->quoteSmart($this->priceConcession),
                     $this->conn->quoteSmart($this->ticketLink),
                     $this->conn->quoteSmart($this->user_name_responsible),
                     $this->conn->quoteSmart($this->user_phone_responsible),
                     $this->conn->quoteSmart($this->user_email_responsible),
                     $this->conn->quoteSmart($this->user_name_ext_contact),
                     $this->conn->quoteSmart($this->user_phone_ext_contact),
                     $this->conn->quoteSmart($this->user_email_ext_contact),
                     $this->conn->quoteSmart($this->user_role_ext_contact),
                     $this->conn->quoteSmart($this->needPosters),
                     $this->conn->quoteSmart($this->picture),
                     $this->conn->quoteSmart($this->getLinks())
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        notify("Repetisjon av arrangement registrert.");
      }else {
        error("New concert: " . $result->toString());
        notify("Arrangementet lot seg ikke repetere.");
        reportBug("arrangement");
      }
    }
  }

  public function copy($data){
      $id = getNextId("program");
      $sql = sprintf("INSERT INTO program 
                          (id, tittel, tittel_en, ingress, ingress_en, type, arr, tid, tekst, tekst_en, kommentar,
                           sted, vpris, mpris, billett, ansv, ansv_tlf, ansv_epost,
                           ekst_navn, ekst_tlf, ekst_epost, ekst_rolle, plakat_behov, vedlegg, linker)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
                           %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($id),
                     $this->conn->quoteSmart($this->name),
                     $this->conn->quoteSmart($this->name_en),
                     $this->conn->quoteSmart($this->intro),
                     $this->conn->quoteSmart($this->intro_en),
                     $this->conn->quoteSmart($this->concertcategory_id),
                     $this->conn->quoteSmart($this->host_id),
                     $this->conn->quoteSmart(scriptParam("newDate")),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->text_en),
                     $this->conn->quoteSmart($this->comment),
                     $this->conn->quoteSmart($this->venue_id),
                     $this->conn->quoteSmart($this->priceNormal),
                     $this->conn->quoteSmart($this->priceConcession),
                     $this->conn->quoteSmart($this->ticketLink),
                     $this->conn->quoteSmart($this->user_name_responsible),
                     $this->conn->quoteSmart($this->user_phone_responsible),
                     $this->conn->quoteSmart($this->user_email_responsible),
                     $this->conn->quoteSmart($this->user_name_ext_contact),
                     $this->conn->quoteSmart($this->user_phone_ext_contact),
                     $this->conn->quoteSmart($this->user_email_ext_contact),
                     $this->conn->quoteSmart($this->user_role_ext_contact),
                     $this->conn->quoteSmart($this->needPosters),
                     $this->conn->quoteSmart($this->picture),
                     $this->conn->quoteSmart($this->getLinks())
                     );
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      notify("Arrangement kopiert.");
      $GLOBALS['extraScriptParams']['concertid'] = $id;
    }else {
      error("New concert: " . $result->toString());
      notify("Problemer med kopiering av arrangement.");
      reportBug("arrangement");
    }
  }
  

  public function _retrieveData(){
    $sql = "SELECT 
              c.tittel AS name,  
              c.tittel_en AS name_en,  
              c.ingress AS intro,  
              c.ingress_en AS intro_en,  
              c.tekst AS text,  
              c.tekst_en AS text_en,  
              c.type AS concertcategory_id,  
              c.arr AS host_id,  
              d.name AS host_name,  
              c.tid AS time,  
              c.sted AS venue_id,  
              v.navn AS venue_name,  
              c.kommentar AS comment,  
              c.billett AS ticketLink,  
              c.facebook AS facebookLink,  
              c.vpris AS priceNormal,  
              c.mpris AS priceConcession,  
              c.vedlegg AS picture,
              c.ekst_navn AS user_name_ext_contact,
              c.ekst_tlf AS user_phone_ext_contact,
              c.ekst_epost AS user_email_ext_contact,
              c.ekst_rolle AS user_role_ext_contact,
              c.plakat_behov AS needPosters,
              c.visUkeprogram AS viewWeekprogram
            FROM program c, lokaler v, din_division d
            WHERE c.id = $this->id
            AND c.sted = v.id
            AND c.arr = d.id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }else {
        notify("Ingen arrangementsdata er registrert.");
        notify($sql);
        error($this->id);   
      }
    }else {
      error("Concerts: " . $result->toString());
      notify("Problemer med henting av arrangementsdata.");        
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM program 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Arrangement slettet.");
    }else {
      error($result->toString());
      notify("Feil under sletteing av arrangement.");
    }
  }

  public function display(){
    //$this->_markAsRead();
    $links = $this->getLinks("DB");
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, CONCERT, "concert", "view-edit-options-concert");
  
    if ($this->picture != 0){
    print('<div class="primary_image"><img src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/program/'.$this->picture.'&amp;maxwidth=200\" alt="pressebilde" /></div>');
  }
?>    <h3><?php print $this->name; ?></h3>
    <p class="date"><?php print formatDatetime($this->time); ?>, <?php print $this->venue_name; ?></p>				 
    <p class="intro"><?php print prepareForHTML($this->intro); ?></p>
    <p><?php print prepareForHTML($this->text); ?></p>

<?php
     if ($links != ''){
?>
    <h4>relevante linker:</h4>
    <p><?php print prepareForHTML($links); ?></p>
<?php } ?>

    <p>
      <strong>Billettpris:</strong> <?php print $this->priceNormal; ?>,-
    </p>
    <p>
      <strong>Medlemspris:</strong> <?php print $this->priceConcession; ?>,-
    </p>
    <p>
      <strong>Arrangør:</strong>
      <a href="index.php?page=display-division&amp;divisionid=<?php print $this->host_id; ?>">
        <?php print $this->host_name; ?>
      </a>
    </p>
    <div class="clear-right">&nbsp;</div>
		</div>
	<?php
    if (checkAuth("view-edit-options-concert") || checkResponsible()){

			$this->_displayReport();

      $freqOptions = Array(Array("id" => "daily", "title" => "daglig"), 
                           Array("id" => "weekly", "title" => "ukentlig"),
                           Array("id" => "biweekly", "title" => "annenhver uke"),
                           Array("id" => "monthlyDate", "title" => "månedlig"),
                           Array("id" => "annual", "title" => "årlig")
                           );
                           
      $title   = "repeat concert";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=repeat-concert&amp;page=display-concerts";
      $fields  = Array(Array("label" => "concertid", "type" => "hidden",
                             "attributes" => Array("name" => "concertid", "value" => "$this->id")),
                       Array("label" => "gjenta aktivitet", "type" => "select", 
                             "attributes" => Array("name" => "frequency", "values" => $freqOptions,
                                                   "value" => $freqOptions[0])),
                       Array("label" => "antall ganger", "type" => "text",
                             "attributes" => Array("name" => "count", "size" => 3, "maxlength" => 3,
                                                   "value" => 0))
                       );
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");


      $title   = "copy concert";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=copy-concert&amp;page=display-concert";
      $fields  = Array(Array("label" => "concertid", "type" => "hidden",
                             "attributes" => Array("name" => "concertid", "value" => "$this->id")),
                       Array("label" => "kopier til", "type" => "datetime", 
                             "attributes" => Array("name" => "newDate", "value" => "$this->time"))
                       );
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");
    }
     ?>
    <div class="clear">&nbsp;</div>
    
<?php
  }

  public function displayList(){
    //$read = $this->_readByCurrentUser();
   ?>
      <tr>
        <td><a class="<?php //($read == true) ? print('read'): print('unread'); ?>" href="index.php?page=display-concert&amp;concertid=<?php print $this->id; ?>"><?php print $this->name; ?></a></td>
        <td><?php print $this->concertcategory_id; ?></td>
        <td><?php print $this->venue_name; ?></td>
        <td><?php print $this->host_name; ?></td>
        <td class="date"><?php print formatDatetime($this->time, "td"); ?></td>				 
        <?php displayOptionsMenuTable($this->id, CONCERT, "concert", "view-edit-options-concert"); ?>
      </tr>
    
<?php
  }  

	public
	function _displayReport() {
    ?>
    <span class="button" onclick="toggleDisplay('report-form-<?php print $this->id; ?>'); toggleText(this, 'vis arrangementsrapport', 'skjul arrangementsrapport');">vis arrangementsrapport</span>
    <div id="report-form-<?php print $this->id; ?>" style="display: none;">
    <?php
    $sql = "SELECT id FROM din_concert_report WHERE concert_id = $this->id";
    $result = $this->conn->query($sql);
    if (DB::isError($result) == true){
      error("Concert Report: " . $result->toString());
    }else {
      if ($result->numRows() != 1) {
          
    	$title   = "registrér arrangementsrapport";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=register-concertreport&amp;page=display-concert";
      $fields  = Array();
      
      $fields[] = Array("label" => "concertid", "type" => "hidden",
                             "attributes" => Array("name" => "concertid", "value" => "$this->id"));

    	$fields[] = Array("label" => "besøkende", "type" => "text", 
                      "attributes" => Array("name" => "visitors","size" => 4,
                                            "maxlength" => 4, 
                                            "value" => (isset($data['visitors'])) ? $data['visitors'] : ""));
    	$fields[] = Array("label" => "resultat", "type" => "text", 
                      "attributes" => Array("name" => "result","size" => 7,
                                            "maxlength" => 8, 
                                            "value" => (isset($data['result'])) ? $data['result'] : ""));
		 	$fields[] = Array("label" => "markedsføring", "type" => "textarea", 
                      "attributes" => Array("name" => "marketing_comment", "cols" => 70,
                      											"rows" => 15, "class" => "mceEditor"));

		 	$fields[] = Array("label" => "produksjon", "type" => "textarea", 
                      "attributes" => Array("name" => "production_comment", "cols" => 70,
                      											"rows" => 15, "class" => "mceEditor"));
			                          
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display();
      	
    	}else {
      	$row =& $result->fetchRow(DB_FETCHMODE_ORDERED);
	      $report_id = $row[0];
				$report = new ConcertReport($report_id);
				$report->display();
    	}
     	print("</div>");
    }
	}

  public function getLinks($type = "text"){
    $sql = "SELECT linker AS url FROM program WHERE id = $this->id";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $row =& $result->fetchRow(DB_FETCHMODE_ORDERED);
      return $row[0];
    }else {
      error("Linker: " . $result->toString());
    }
    
    $sql = "SELECT url FROM program_linker
            WHERE parent_id = $this->id";
          
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      if ($type == "DB"){
        return $result;
      }else if ($type == "text"){
        $a = "";
        while ($row =& $result->fetchRow(DB_FETCHMODE_ORDERED)){
          $a .= $row[0]."\r\n";
        }
        return $a;
      }
    }else {
      error("Linker: " . $result->toString());
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
           
    $sql = sprintf("INSERT INTO din_concertread VALUES
                    (%s, %s, NOW())",
                   $this->conn->quoteSmart($this->id),
                   $this->conn->quoteSmart($user)
                   );
    $result = $this->conn->query($sql);
    if (DB::isError($result) == true){
      notify($result->toString());
    }
  }

  public function _readByCurrentUser(){
    $user = getCurrentUser();
    if ($user == false){
      return false;
    }

    $sql = sprintf("SELECT id FROM din_concertread 
                    WHERE id = %s
                    AND userId = %s",
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
