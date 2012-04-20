<?php

class Documents {
  var $conn;

  function Documents(){
    $this->__construct();
  }
  
  function __construct(){
    $this->conn = db_connect();
  }

  public 
  function display($limit = 5, $selection = "0000-00-00"){

    $sql = "SELECT id FROM din_document
            ORDER BY date DESC
            WHERE date > $selection
            LIMIT $limit";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){

        displayOptionsMenu($row['id'], "document");
        $document = new Document($row['id']);
        $document->display();
      }

    }else {
      error("Documents: " . $result->toString());
    }
    
  }

  public 
  function displayList($category = 0){
    if ($category > 0){
      $cat_condition = "AND d.documentcategory_id = $category"; 
    }else {
      $cat_condition = ""; 
    }
    $sql = "SELECT d.*, dc.title AS category
            FROM din_document d, din_documentcategory dc
            WHERE d.documentcategory_id = dc.id " .
           "$cat_condition " .
           "ORDER BY name ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($result->numRows() > 0){
?>
      <table class="sortable" id="documentlist">
        <tr>
          <th>tittel</th>
          <th>kategori</th>
          <th>tags</th>
          <th>endret</th>
          <th>størrelse</th>
          <?php if(checkAuth("view-edit-options-document")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){?>
        <tr>
          <td><a href="inside/file.php?id=<?php print $row->id; ?>"><?php print $row->name; ?></a></td>
          <td><?php print $row->category; ?></td>
          <td><?php print $this->_tags_to_links($row->tags); ?></td>
          <td><?php print formatDate($row->date, "td"); ?></td>
          <td><?php printf("%.2f KB", $row->size/1024); ?></td>
          <?php displayOptionsMenuTable($row->id, DOCUMENT, "document", "view-edit-options-document"); ?>
        </tr>

<?php
          }
        print("      </table>");
      }else {
        print("<p>Ingen dokumenter registrert.</p>");
      }
    }else {
      error("Documents: " . $result->toString());
    }
    
  }


  public 
  function displayListTag($tag = NULL){
    if ($tag == NULL){
      return false;
    }
    $sql = "SELECT d.*, dc.title AS category
            FROM din_document d, din_documentcategory dc
            WHERE d.documentcategory_id = dc.id " .
           "AND tags LIKE '%$tag%' " .
           "ORDER BY name ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      if ($result->numRows() > 0){
?>
      <table class="sortable" id="documentlist">
        <tr>
          <th>tittel</th>
          <th>kategori</th>
          <th>tags</th>
          <th>endret</th>
          <th>størrelse</th>
          <?php if(checkAuth("view-edit-options-document")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
          while ($row =& $result->fetchRow(DB_FETCHMODE_OBJECT)){?>
        <tr>
          <td><a href="inside/file.php?id=<?php print $row->id; ?>"><?php print $row->name; ?></a></td>
          <td><?php print $row->category; ?></td>
          <td><?php print $this->_tags_to_links($row->tags); ?></td>
          <td><?php print formatDate($row->date, "td"); ?></td>
          <td><?php printf("%.2f KB", $row->size/1024); ?></td>
          <?php displayOptionsMenuTable($row->id, DOCUMENT, "document", "view-edit-options-document"); ?>
        </tr>

<?php
          }
        print("      </table>");
      }else {
        print("<p>Ingen dokumenter registrert.</p>");
      }
    }else {
      error("Documents: " . $result->toString());
    }
    
  }
  
  public
  function _tags_to_links($tags){
    $tags = explode(" ", $tags);
    $tag_list = "";
    foreach ($tags as $tag){
      $tag_list .= "<a href=\"index.php?page=display-documents&amp;documenttag=$tag\">$tag</a> ";
    }
    return $tag_list;       
  }

}


?>
