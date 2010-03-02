<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Article {

  public $id;
  public $title;
  public $intro;
  public $text;
  public $attachment1;
  public $caption1;
  public $attachment2;
  public $caption2;
  public $date;
  public $author;
  public $updated_by;
  public $updated_at;
  public $expires;


  public $conn;

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New article
      if ($data == NULL){
        error('Article: No data supplied.');
      }else {
		$this->author = getCurrentUserName();
		$this->updated_by = getCurrentUserName();


      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article
				$this->updated_by = getCurrentUserName();

        //primary attachment
        if($_FILES['attachment1']['error'] != 4){
          $temp_name = new_file($_FILES['attachment1'], 'nyheter');
          rename_file($temp_name, $this->id, 'nyheter');
          $this->attachment1 = $this->id.substr($temp_name, -4);
        }else {
          $this->attachment1 = 0;
        }

        //secondary attachment
        if($_FILES['attachment2']['error'] != 4){
          $temp_name = new_file($_FILES['attachment2'], 'nyheter');
          rename_file($temp_name, $this->id.'_2', 'nyheter');
          $this->attachment2 = $this->id.'_2'.substr($temp_name, -4);
        }else {
          $this->attachment2 = 0;
        }

      }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
		    $this->date  			 = $data['date'];
		    $this->attachment1 = $data['attachment1'];
    		$this->attachment2 = $data['attachment2'];
    		$this->author 		 = $data['author'];
    		$this->updated_by  = $data['updated_by'];
    		$this->updated_at  = $data['updated_at'];
      }
    }
    //Common initializations
    $this->title    = htmlspecialchars(stripslashes($data['title']));
    $this->intro 	  = stripcslashes($data['intro']);
    $this->text     = stripcslashes($data['text']);
    $this->caption1 = $data['caption1'];
    $this->caption2 = $data['caption2'];
    $this->expires 	= $data['expires'];
  }

  public function store(){
    if ($this->id == NULL){
      $this->id = getNextId("nyhet");
      $sql = "INSERT INTO nyhet VALUES " .
      			 "  (%s, %s, %s, %s, '', %s, '', %s, NOW(), %s, %s, NOW(), %s)";
			$sql = sprintf($sql,
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->intro),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->caption1),
                     $this->conn->quoteSmart($this->caption2),
                     $this->conn->quoteSmart($this->author),
                     $this->conn->quoteSmart($this->updated_by),
                     $this->conn->quoteSmart($this->expires)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){

        //primary attachment
        if($_FILES['attachment1']['error'] != 4){
          $temp_name = new_file($_FILES['attachment1'], 'nyheter');
          rename_file($temp_name, $this->id, 'nyheter');
          $this->attachment1 = $this->id.substr($temp_name, -4);
        }else {
          $this->attachment1 = 0;
        }

        //secondary attachment
        if($_FILES['attachment2']['error'] != 4){
          $temp_name = new_file($_FILES['attachment2'], 'nyheter');
          rename_file($temp_name, $this->id.'_2', 'nyheter');
          $this->attachment2 = $this->id.'_2'.substr($temp_name, -4);
        }else {
          $this->attachment2 = 0;
        }
        $this->_storeFilenames();

        $GLOBALS['extraScriptParams']['articleid'] = $this->id;
        notify("Nyhet er lagret.");
      }else {
        error("New article: " . $result->toString());
      }
    }else {
      $sql = sprintf("UPDATE nyhet SET
                        tittel   = %s,
                        ingress  = %s,
                        tekst    = %s,
                        utgar    = %s,
                        caption1 = %s,
                        caption2 = %s,
                        sistEndretAv = %s,
                        sistEndret   = NOW()
                      WHERE
                        id = %s",
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->intro),
                     $this->conn->quoteSmart($this->text),
                     $this->conn->quoteSmart($this->expires),
                     $this->conn->quoteSmart($this->caption1),
                     $this->conn->quoteSmart($this->caption2),
                     $this->conn->quoteSmart($this->updated_by),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
          notify("Nyhet oppdatert.");
		  $this->_storeFilenames();
      }else {
          error("New event: " . $result->toString());
      }
    }
  }

  public function _retrieveData(){
    $sql = "SELECT " .
    			 "  tittel AS title, ".
    			 "  ingress AS intro, ".
    			 "  tekst AS text, ".
    			 "  vedlegg AS attachment1, ".
    			 "  caption1, ".
    			 "  vedlegg2 AS attachment2, ".
    			 "  caption2, ".
    			 "  dato AS date, ".
    			 "  forfatter AS author, ".
    			 "  sistEndretAv AS updated_by, ".
    			 "  sistEndret AS updated_at, ".
    			 "  utgar AS expires " .
    			 "FROM nyhet WHERE id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Articles: " . $result->toString());
    }
  }

  public function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM nyhet
            WHERE id = $id
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      notify("Nyhet slettet.");
    }else {
      error($result->toString());
    }
  }

  public function display(){
?>
     <div class="text-column">
<?php
    displayOptionsMenu($this->id, ARTICLE, "article", "view-edit-options-article");

	  print('<div class="article_images">');
      if ($this->attachment1 != 0){
  	    print('<div class="primary_image">');
  	    print('<img src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/nyheter/'.$this->attachment1.'&amp;maxwidth=200" alt="" />');
        print('<span class="caption">'.$this->caption1.'</span>');
	    print('</div>');
	  }

    if ($this->attachment2 != 0){
  	  print('<div class="extra_image">');
  	  print('<img src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/nyheter/'.$this->attachment2.'&amp;maxwidth=200" alt="" />');
	  	print('<span class="caption">'.$this->caption2.'</span>');
	  	print('</div>');
	}
	print('</div>');


	//$text = str_replace("[img2]", $this->_text_insert_attachment($this->attachment2, $this->caption2), $this->text);
	$text = str_replace('[img2]', '', $this->text);

?>
    <h3><?php print $this->title; ?></h3>
    <p class="date"><?php print formatDate($this->date); ?></p>

    <p class="intro"><?php print prepareForHTML($this->intro); ?></p>
    <p class="text"><?php print $text; ?></p>

  <?php
  }

  public function displayList(){
   ?>
      <tr>
        <td><a href="index.php?page=display-article&amp;articleid=<?php print $this->id; ?>"><?php print $this->title; ?></a></td>
        <td><?php print prepareForHTML($this->intro); ?></td>
        <td><?php print $this->author; ?></td>
        <td class="date"><?php print formatDate($this->date, "td"); ?></td>
        <td class="date"><?php print formatDate($this->expires, "td"); ?></td>
        <?php displayOptionsMenuTable($this->id, ARTICLE, "article", "view-edit-options-article"); ?>
      </tr>

<?php
  }

  private function _text_insert_attachment($file, $caption) {
  	$string = "";
  	if (!empty($file)){
    	$string .= '<div class="extra_image">
                  <img class="nyhet" src="http://www.studentersamfundet.no/imageResize.php?pic=bilder/nyheter/'.$file.'&amp;maxwidth=200" alt=" " />';
    	if (!empty($caption)){
      	$string .= '<span class="caption">'.$caption.'</span>';
    	}
    	$string .= "</div>\n";
   	}
  	return $string;
  }

  private function _storeFilenames() {
  	if ($this->attachment1 != 0) {
      $sql = "UPDATE nyhet SET vedlegg = '$this->attachment1' WHERE id = $this->id";
	  $result = $this->conn->query($sql);
    }
    if ($this->attachment2 != 0) {
      $sql = "UPDATE nyhet SET vedlegg2 = '$this->attachment2' WHERE id = $this->id";
	  $result = $this->conn->query($sql);
    }
  }
}

?>