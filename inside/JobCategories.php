<?php

class JobCategories {
  var $categories;
  var $conn;

  function JobCategories(){
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();

    $sql = "SELECT id, title FROM din_jobcategory";
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      $this->categories = $result;
    }
  }

  public function getCategories(){
    return $this->categories;
  }

  public function displayList(){
    ?>
      <table id="job-cat-list">
        <tr>
          <th colspan="2">stillingstyper</th>
          <?php if(checkAuth("view-edit-options-jobCategory")){
        ?><th colspan="2">&nbsp;</th><?php } ?>
        </tr>
<?php      
     while ($row =& $this->categories->fetchRow(DB_FETCHMODE_ASSOC)){
       $jobCat = new JobCategory($row['id']);
       $jobCat->displayList();
     }
    print("      </table>");
  }
}
?>
