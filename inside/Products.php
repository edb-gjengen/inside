<?php

class Products {
  var $conn;

  function Products() {
    $this->__construct();
  }

  function __construct(){
    $this->conn = db_connect();
  }
  
  public function getList(){
  		$sql = "SELECT p.id, p.title " .
  					 "FROM din_product d";

    $result =& $this->conn->query($sql);
    if (DB::isError($result) != true){
      return $result;
    } else {
      error("Products: " . $result->toString());
    }
  }

  public function display(){
    $sql = "SELECT id FROM din_product
            ORDER BY title ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){

        displayOptionsMenu($row['id'], "product");
        $product = new Product($row['id']);
        $product->display();
      }
    }else {
      error("Products: " . $result->toString());
    }
  }

  public function displayList(){
    $sql = "SELECT id FROM din_product
            ORDER BY display_in_shop DESC, title ASC";
    $result =& $this->conn->query($sql);
    
    if (DB::isError($result) != true){?>
      <table class="sortable" id="productlist">
        <tr>
          <th>produktnavn</th>
          <th>beskrivelse</th>
          <th>pris</th>
          <th>aktiv</th>
          <th>solgt</th>
          <th>nye</th>
          <?php if(checkAuth("view-edit-options-product")){
        ?><th colspan="2"></th><?php } ?>
        </tr>
<?php      
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        $product = new Product($row['id']);
        $product->displayList();
      }
      print("      </table>");
    } else {
      error("Products: " . $result->toString());
    }
  }

  public function displayShopProducts(){
    $sql = "SELECT id FROM din_product WHERE display_in_shop = 1
            ORDER BY id DESC";
    $result =& $this->conn->query($sql);
	
    if (DB::isError($result) != true){?>
			<div class="text-column">
			<h2>Produkter tilgjengelig for kjøp:</h2>
<?php
    
    // Vis varen medlemskap hvis brukeren ikke har gyldig medlemskap
	if (membershipExpired(getCurrentUser())){
		$product = new Product(1);
		$product->displayListShop();
	} else {
		$product = new Product(12);
		$product->displayListShop();
	}
	
	// List opp produkter
	while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        $product = new Product($row['id']);
        $product->displayListShop();
    }
      
    ?></div><?php
    } else {
      error("Products: " . $result->toString());
    }
  }
}
?>