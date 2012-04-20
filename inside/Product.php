<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Product {

  var $id;
  var $title;
  var $description;
  var $price;
	var $allow_comment;
	var $display_in_shop;
	var $picture;
	
  var $conn;

  function Product($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){//New division
      if ($data == NULL){
        error("Product: No data supplied.");     
      }
    }else {//ID set, existing article
      if ($data != NULL){//Update existing article
        if($_FILES['userfile']['error'] != 4){
          $temp_name = new_file($_FILES['userfile'], 'products');
          rename_file($temp_name, $this->id, 'products');
          $ext = strtolower( pathinfo($temp_name, PATHINFO_EXTENSION) );
          $this->picture = $this->id . "." . $ext;
        }else {
          $this->picture = 0; 
        }

	     }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
        $this->picture = $data['picture'];
      }
    }
    //Common initializations
    $this->title		   = $data['title'];
    $this->description = $data['description'];
    $this->price       = $data['price'];
    $this->allow_comment    = $data['allow_comment'];
    $this->display_in_shop = $data['display_in_shop'];

  }

  public function store(){
    if ($this->id == NULL){
      $this->id = getNextId("din_product");
      $sql = sprintf("INSERT INTO din_product 
                          (id, title, description, price, allow_comment, display_in_shop)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->description),
                     $this->conn->quoteSmart($this->price),
                     $this->conn->quoteSmart($this->allow_comment),
                     $this->conn->quoteSmart($this->display_in_shop)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        if($_FILES['userfile']['error'] != 4){
          $temp_name = new_file($_FILES['userfile'], 'products');
          rename_file($temp_name, $this->id, 'products');
          $ext = strtolower( pathinfo($temp_name, PATHINFO_EXTENSION) );
          $this->picture = $this->id . "." . $ext;
        }else {
          $this->picture = 0; 
        }

			  $this->_storeFilenames();
        notify("Nytt produkt lagret.");
      }else {
        error("New product: " . $result->toString());
      }
      
    }else {
      $sql = sprintf("UPDATE din_product SET 
                        title            = %s,
                        description      = %s,
                        price            = %s,
                        allow_comment    = %s,
                        display_in_shop = %s
                      WHERE 
                        id = %s",
                     $this->conn->quoteSmart($this->title),
                     $this->conn->quoteSmart($this->description),
                     $this->conn->quoteSmart($this->price),
                     $this->conn->quoteSmart($this->allow_comment),
                     $this->conn->quoteSmart($this->display_in_shop),
                     $this->conn->quoteSmart($this->id)
                     );

      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
			  $this->_storeFilenames();
        notify("Produkt oppdatert.");
      }else {
        notify("Problemer med lagring av produkt. Ingen endringer utført.");
        error("Update product: " . $result->toString());
      }
    }
  }

    private function _storeFilenames() {
        if ($this->picture != 0) {
            $sql = "UPDATE din_product ";
            $sql .= "SET picture = '$this->picture' ";
            $sql .= "WHERE id = $this->id";
	        $result = $this->conn->query($sql);
        }
    }

    public function _retrieveData(){
        $sql = "SELECT * ";
        $sql .= "FROM din_product p ";
        $sql .= "WHERE p.id = $this->id";

        $result =& $this->conn->query($sql);
        
        if (DB::isError($result) != true) {
            if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                return $row;
            }
        } else {
            error("Product: " . $result->toString());
        }
    }

    public function delete($id){
        $conn = db_connect();
        $sql = "DELETE FROM din_product ";
        $sql .= "WHERE id = $id ";
        $sql .= "LIMIT 1";

        $result = $conn->query($sql);

        if (DB::isError($result) != true) {
            if ($conn->affectedRows() > 0) {
                notify("Produktet er slettet.");
            } else {
                notify("Ugyldig produktid, ingen handling utført.");        
            }
        } else {
            error($result->toString());
        }
    }

    // return number of sold items
    public function getNumSold() {
        $sql = "SELECT SUM(oi.quantity) AS num ";
        $sql .= "FROM din_product p, din_order_item oi, din_order o ";
        $sql .= "WHERE oi.product_id = p.id AND oi.order_id = o.id AND o.order_status_id = 4 AND p.id = $this->id";

        $result =& $this->conn->query($sql);

        if (DB::isError($result) != true) {
            if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                if ($row['num'] > 0) return $row['num'];
                else return 0;
            }
        } else {
            error("Product: " . $result->toString());
        }
    }

    // is it in shop ?
    public function getDisplay() {
        if ($this->display_in_shop) return "Ja";
        return "Nei";
    }

    // return number of items not marked as delivered
    public function getNumNew(){
        $sql = "SELECT COUNT(*) AS num ";
        $sql .= "FROM din_product p, din_order_item oi, din_order o ";
        $sql .= "WHERE oi.product_id = p.id AND oi.order_id = o.id AND o.order_status_id = 4 AND p.id = $this->id AND order_deliverystatus_id='0'";

        $result =& $this->conn->query($sql);

        if (DB::isError($result) != true) {
            if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                return $row['num'];
            }
        } else {
            error("Product: " . $result->toString());
        }
    }

  public function display(){
?>
		<div class="product">
			<?php
      if ($this->picture != 0){
		print('<div class="product_images">');
  	    print('<img src="inside/imageResize.php?pic=images/products/'.$this->picture.'&amp;maxwidth=200" alt="" />');
	    print('</div>');
	  }
			?>
			<h4><?php print $this->title; ?></h4>
      <p><?php print prepareForHTML($this->description); ?></p>
      <p><strong>Pris:</strong> <?php print formatPrice($this->price); ?></p>
    	<div class="clear-right"></div>
      <?php
      
      
      $order_id = scriptParam("order_id");
      if (!is_numeric($order_id )){
      	$order_id = NULL;
      }
      
      $title   = "legg i handlekurv";
      $enctype = NULL;
      $method  = "post";
      $action  = "index.php?action=cart-add-product&amp;page=display-cart";
      $fields  = Array();
    
      $fields[] = Array("label" => "", "type" => "cart_product",
                       "attributes" => Array("name" => "cart_product", "product_id" => $this->id, "order_id" => $order_id, "allow_comment" => $this->allow_comment));
      $form = new Form($title, $enctype, $method, $action, $fields);
      $form->display("horizontal");
      ?>
    </div>
     
<?php
  }

  public function displayListShop(){
?>
		<div class="product" onclick="window.location='index.php?page=display-product&amp;productid=<?php print $this->id; ?>'">
			<?php
      if ($this->picture != 0){
		print('<div class="product_images">');
  	    print('<img src="inside/imageResize.php?pic=images/products/'.$this->picture.'&amp;maxheight=100" alt="" />');
	    print('</div>');
	  }
			?>
			<h4><?php print $this->title; ?></h4>
      <p><strong>Pris:</strong> <?php print formatPrice($this->price); ?></p>
    	<div class="clear-right"></div>
    </div>
     
<?php
  }

  public function displayList(){
   if (checkAuth('view-edit-product')) {
     $page = 'edit-product';
   }else {
     $page = 'display-sales-item';
   }
   ?>
      <tr>
        <td><a href="index.php?page=<?php print $page; ?>&amp;productid=<?php print $this->id; ?>"><?php print $this->title; ?></a></td>
	      <td><?php print prepareForHTML($this->description); ?></td>
        <td><?php print formatPrice($this->price); ?></td>
        <td align="center"><?php print $this->getDisplay();?></td>
        <td align="right"><?php print $this->getNumSold(); ?></td>
        <td align="right"><?php print $this->getNumNew(); ?></td>
        <?php displayOptionsMenuTable($this->id, PRODUCT, "product", "view-edit-options-product"); ?>
      </tr>
<?php
  }  
  
  public function displayBuyers() {
    $sql = "SELECT o.id AS order_id, u.id, oi.quantity, t.timestamp, oi.comment, o.order_deliverystatus_id
FROM din_user u, din_product p, din_order o, din_order_item oi, din_transaction t
WHERE u.id = o.user_id
AND oi.order_id = o.id
AND oi.product_id = p.id
AND o.order_status_id = 4 AND t.order_id = o.id
AND t.status = 'OK' " .
"AND p.id = $this->id";

    $result =& $this->conn->query($sql);

    if (DB::isError($result) == true){
			notify('Liste over kjøpere er ikke tilgjengelig.');
			error('List buyers: ' . $result->toString());
    }
		?>
		<table>
			<tr>
				<th>#</th>
				<th>kjøper</th>
				<th>antall</th>
				<th>status</th>
				<th>endre status</th>
				<th>dato</th>
			</tr>
		<?php
	
    while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
				$user = new User($row['id']);
				?>
				<tr>
					<td><?php print $row['order_id']; ?></td>
					<td><a href="index.php?page=display-user&amp;userid=<?php print $user->id; ?>"><?php print $user->getName(); ?></a><br />
						<?php print prepareForHTML($row['comment']); ?></td>
					<td><?php print $row['quantity']; ?></td>
					<td id="order_<?php print $row['order_id']?>_deliverystatus"><?php
		if ($row['order_deliverystatus_id'] == '1') {
			print 'levert';
		} else {
			print 'ikke levert';
		}
					?></td>
					<td>
						<form id="order_<?php print $row['order_id']?>_update_form" action="javascript: setOrderDeliveryStatus('<?php print $row['order_id']; ?>')" method="get">
						<input type="hidden" name="action" value="update-order-deliverystate" />
						<select name="newDeliveryStatus_<?php print $row['order_id']; ?>" id="newDeliveryStatus_<?php print $row['order_id']; ?>">
							<option value="0">ikke levert</option>
							<option value="1" selected>levert</option>
						</select>
						<input type="submit" value="endre" />
						</form>
					</td>
					<td class="date"><?php print formatDatetimeYearShort($row['timestamp']); ?></td>
				</tr>
	<?php
    }
    ?>
    </table>
    <?php
  }
}

?>
