<?php


/*
 *This class requires PEAR::DB and functions library
 */
class OrderItem {

  var $id;
  var $product_id;
  var $quantity;
 	var $order_id;
 	var $discount;
 	var $comment;

  var $conn;

  function OrderItem($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public 
  function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){// OrderItem

    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

	    }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
      }
    }
    //Common initializations
		$this->product_id = $data['product_id'];
		$this->quantity   = $data['quantity'];
		$this->order_id   = $data['order_id'];
		$this->discount   = $data['discount'];
    $this->comment		= isset($data['comment']) ? $data['comment'] : '';
  }

  public function store(){
    if ($this->id == NULL){
      $this->id = getNextId("din_order_item");
      $sql = sprintf("INSERT INTO din_order_item 
                          (id, product_id, quantity, order_id, discount, comment)
                      VALUES 
                          (%s, %s, %s, %s, %s, %s)", 
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->product_id),
                     $this->conn->quoteSmart($this->quantity),
                     $this->conn->quoteSmart($this->order_id),
                     $this->conn->quoteSmart($this->discount),
                     $this->conn->quoteSmart($this->comment)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        //notify("Nytt ordreelement lagret.");
      }else {
        error("New order_item: " . $result->toString());
      }  
    }
  }
  
  
  public function _retrieveData(){
    $sql = "SELECT *
            FROM din_order_item t
            WHERE t.id = $this->id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("OrderItem: " . $result->toString());
    }
  }

  public
  function setQuantity($value) {
    $sql = "UPDATE din_order_item SET
                quantity = $value
            WHERE " .
           "		id = $this->id";
                   
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      //notify("Orderstatus oppdatert ($value)");
    }else {
      error("Set quantity -  order_item: " . $result->toString());
    }  	
  }

  public
  function setComment($value) {
    $sql = "UPDATE din_order_item SET
                comment = '$value'
            WHERE " .
           "		id = $this->id";
                   
    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      //notify("Orderstatus oppdatert ($value)");
    }else {
      error("Set comment -  order_item: " . $result->toString());
    }  	
  }

  public
  /*static*/ 
  function delete($id){
    $conn = db_connect();
    $sql = "DELETE FROM din_order_item 
            WHERE id = $id 
            LIMIT 1";
    $result = $conn->query($sql);
    if (DB::isError($result) != true){
      if ($conn->affectedRows() > 0){
        //notify("Varen er slettet fra handlekurven.");
      }else {
        //notify("Ugyldig order_itemid, ingen handling utført.");        
      }
    }else {
      error($result->toString());
    }
  }
  
  public function display(){
		$product = new Product($this->product_id);
?>
		<div class="text-column">
			<h3>Ordrebekreftelse</h3>
      <p>Ditt kjøp er gjennomført. På kontoutskriften din vil det stå Payex AS.</p>
      <p>Ordren har referansen #<?php print $this->id;?>.</p>

		  <table>
    		<tr>
		      <th>produkt</th><th>pris</th>
    		</tr>
		    <tr>
    		  <td><strong><?php print $product->title; ?></strong><br /><?php print $product->description; ?></td><td><?php print formatPrice($this->price); ?></td>
		    </tr>
  		</table>
  
  		<p>Du vil få tilsendt medlemskort i posten i løpet av 6-12 dager.</p>
  		<p>Om du allerede har medlemskort vil du kun få tilsendt nytt oblat for inneværende år.</p>
  		<p>For spørsmål vedrørende medlemskapet eller transaksjonen kan du kontakte <a href="mailto:support@studentersamfundet.no">support@studentersamfundet.no</a>.</p>
   
    </div>
     
<?php
  }

  public 
  function displayList(){
   	$product = new Product($this->product_id);
   ?>
      <tr>
        <td><strong><?php print $product->title; ?></strong><br />
        <?php print prepareForHTML($product->description); ?><br />
        <?php if ($product->allow_comment == 1) { ?>
        <strong>Kommentar:</strong><br />
        <textarea name="ordercomment<?php print $this->id; ?>" cols="50" rows="2"><?php print prepareForHTML($this->comment); ?></textarea>
        <?php } ?>
        </td>
	      <td><?php print formatPrice($product->price); ?></td>
	      <td><input type="text" name="orderitem<?php print $this->id; ?>" size="2" value="<?php print $this->quantity; ?>" /></td>
      </tr>
<?php
  }  

  public 
  function displayShortList(){
   	$product = new Product($this->product_id);
   ?>
      <tr>
        <td><?php print $product->title; ?></td>
	      <td><?php print formatPrice($product->price); ?></td>
	      <td><?php print $this->quantity; ?></td>
      </tr>
<?php
  }  
	
	public
	function getConfirmationText() {
		$product = new Product($this->product_id);
		$text = $this->quantity . " " . $product->title . " á " . formatPrice($product->price) . "\n";
		return $text; 
	}
}

?>