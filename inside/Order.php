<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Order {

  var $id;
  var $user_id;
  var $timestamp;
 	var $order_status_id;
 	var $order_status_value;
 	var $comment;

  var $conn;

  function Order($id = NULL, $data = NULL){
    $this->__construct($id, $data);
  }

  public
  function __construct($id = NULL, $data = NULL){
    $this->conn = db_connect();

    $this->id = $id;

    if ($id == NULL){// Order
	    $this->order_status_id = 1;//New empty order created
	    $this->comment = "Order created.";
	    $this->user_id = getCurrentUser();

    }else {//ID set, existing article
      if ($data != NULL){//Update existing article

	     }else {//Retrieve data from backend for display or other actions
        $data = $this->_retrieveData();
		    $this->user_id = $data['user_id'];
      	$this->timestamp = $data['timestamp'];
		    $this->order_status_id    = $data['order_status_id'];
		    $this->order_status_value = $data['order_status_value'];
		    $this->comment						= $data['comment'];

      }
    }
    //Common initializations
  }

  public
  function store(){
    if ($this->id == NULL){
      $this->id = getNextId("din_order");
      $sql = sprintf("INSERT INTO din_order
                          (id, user_id, timestamp, comment)
                      VALUES
                          (%s, %s, NOW(), %s)",
                     $this->conn->quoteSmart($this->id),
                     $this->conn->quoteSmart($this->user_id),
                     $this->conn->quoteSmart($this->comment)
                     );
      $result = $this->conn->query($sql);
      if (DB::isError($result) != true){
        //notify("Ny ordre lagret.");
      }else {
        error("New order: " . $result->toString());
      }
    }
  }

  public
  function setStatus($value) {
    $sql = "UPDATE din_order SET
                order_status_id = $value
            WHERE " .
           "		id = $this->id";

    $result = $this->conn->query($sql);
    if (DB::isError($result) != true){
      //notify("Orderstatus oppdatert ($value)");
    }else {
      error("Set status -  order: " . $result->toString());
    }
  }

  public
  function addOrderItem($data) {
	$data['order_id'] = $this->id;
	$data['discount'] = 0;

  	$added = false;

  	// fetch items in order and check if item allready is in order
  	$items = $this->getItems();
	while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
		$item = new OrderItem($row['id']);
		if ($item->product_id == $data['product_id']) {
			// item already exists in order, update quantity
			$item->setQuantity($item->quantity + $data['quantity']);
			$added = true;
		}
	}

  	// new product id for this order, add order item
  	if (!$added) {
		if($data['product_id'] == 27)
		{
			$user = new User($this->user_id);
			if($user->expires != '2011-12-31')
			{
				notify('Du ser ikke ut til å ha hatt medlemskap for 2011 - vennligst kontakt billettluka om du mener dette er en feil');
				return false;
			}
		}
		$item = new OrderItem(NULL, $data);
		$item->store();
  	}

	return true;
  }

  public
  function _updateTotalPrice(){

  }

  public
  function _retrieveData(){
    $sql = "SELECT o.*, os.value AS order_status_value
            FROM din_order o, din_order_status os
            WHERE o.id = $this->id " .
           "AND o.order_status_id = os.id";
    $result =& $this->conn->query($sql);

    if (DB::isError($result) != true){
      if ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
        return $row;
      }
    }else {
      error("Order: " . $result->toString());
    }
  }

  public
  /*static*/
  function delete($id){
		$order = new Order($id);
		$order->setStatus(5);
  }

  public
  function getItems() {
  	$sql = "SELECT oi.id " .
  				 "FROM din_order_item oi " .
  				 "WHERE oi.order_id = $this->id";
    $result =& $this->conn->query($sql);
		return $result;
  }

  public
  function calculateTotalAmount() {
		$items = $this->getItems();
		$amount = 0;

    while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
      $item = new OrderItem($row['id']);
      $product = new Product($item->product_id);
      $amount = $amount + $item->quantity * $product->price;
    }
		return $amount;
  }

  public
  function display(){
		$items = $this->getItems();
?>
		<div class="text-column">
			<h3>Din handlekurv</h3>
<?php
	  if (DB::isError($items) != true){
    	if ($items->numRows() > 0){?>

      <form action="index.php?page=display-cart" method="post" id="update-order-<?php print $this->id; ?>">
  		<div><input type="hidden" name="action" value="update-cart" /></div>

      <table class="sortable" id="cartcontent">
        <tr>
          <th>vare</th>
          <th>pris</th>
          <th>antall</th>
        </tr>
<?php
    	}else {
    		print("Ingen varer er registrert.");
    	}
      while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
        $item = new OrderItem($row['id']);
        $item->displayList();
      }
      ?>
      <tr class="table-footer">
      	<td>Sum:</td>
      	<td colspan="2"><?php print formatPrice($this->calculateTotalAmount()); ?></td>
      </tr>
      </table>
      <p>Har du gjort endringer? <span class="button" onclick="document.getElementById('update-order-<?php print $this->id; ?>').submit();">oppdatér</span></p>
      </form>
      <p>Handle mer? <a class="button" href="index.php?page=display-webshop">tilbake til produkter</a></p>

      <form action="index.php?action=cart-checkout" method="post">
	  		<div>
	  			<input type="hidden" name="order_id" value="<?php print $this->id; ?>" />
	  			<input type="hidden" name="transaction_id_string" value="<?php print createTransactionId(); ?>" /></div>
	  		<p>Er du ferdig? <a class="button" href="index.php?action=cart-checkout&amp;order_id=<?php print $this->id; ?>&amp;transaction_id_string=<?php print createTransactionId(); ?>">gå til betaling</a></p>
      </form>
      <?php
    }else {
      error("Items: " . $result->toString());
    }
?>
    </div>

<?php
  }

  public
  function displayShortList($edit = false){
		$items = $this->getItems();
   ?>
		<div class="text-column">
			<h3>Handlekurv #<?php print $this->id; ?></h3>
<?php
	  if (DB::isError($items) != true){
?>
      <table class="sortable" id="cartcontent">
        <tr>
          <th>vare</th>
          <th>pris</th>
          <th>antall</th>
        </tr>
<?php
      while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
        $item = new OrderItem($row['id']);
        $item->displayShortList();
      }
      ?>
      <tr class="table-footer">
      	<td>Sum:</td>
      	<td colspan="2"><?php print formatPrice($this->calculateTotalAmount()); ?></td>
      </tr>
      </table>
			<p>Ordrestatus: <strong><?php print $this->order_status_value; ?></strong>
			<?php if ($edit && $this->order_status_id < 3) { ?>
			<p>
				<a class="button" href="index.php?page=display-cart&amp;order_id=<?php print $this->id; ?>">rediger ordre</a>
				<a class="button" href="index.php?page=display-carts&amp;action=delete-order&amp;order_id=<?php print $this->id; ?>">slett ordre</a>
				<a class="button" href="index.php?action=cart-checkout&amp;order_id=<?php print $this->id; ?>&amp;transaction_id_string=<?php print createTransactionId(); ?>">gå til betaling</a>
			</p>
      <?php }
    }else {
      error("Items: " . $result->toString());
    }
?>
    </div>
<?php
  }

  public
  function getConfirmationText() {
  	$items = $this->getItems();
    $text = "";
    while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
      $item = new OrderItem($row['id']);
      $text .= $item->getConfirmationText();
    }
  	return $text;
  }

  public
  function displayConfirmation(){
		$items = $this->getItems();
?>
<?php
	  if (DB::isError($items) != true){
    	if ($items->numRows() > 0){?>

	    <table class="sortable" id="cartcontent">
        <tr>
          <th>vare</th>
          <th>pris</th>
          <th>antall</th>
        </tr>
<?php
    	}else {
    		print("Ingen varer er registrert.");
    	}

      while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
        $item = new OrderItem($row['id']);
        $item->displayShortList();
      }
      ?>
      <tr class="table-footer">
      	<td>Sum:</td>
      	<td colspan="2"><?php print formatPrice($this->calculateTotalAmount()); ?></td>
      </tr>
      </table>
      <?php
    }else {
      error("Items: " . $result->toString());
    }
  }

	public function performOperations() {
		$items = $this->getItems();
		while ($row =& $items->fetchRow(DB_FETCHMODE_ASSOC)){
			$item = new OrderItem($row['id']);

			if ($item->product_id == 1) {
				// new membership
				$user = new User($this->user_id);
				$cardno = $user->cardno;
				if ($cardno == NULL) {
					if (!$user->registerMembershipPayex()){
						return false;
					}
				} else {
					if(!$user->renewMembershipPayex()){
						return false;
					}
				}
  			} elseif ($item->product_id == 12) {
  				// send new membercard
  				$user = new User($this->user_id);
  				if (!$user->renewMembercardPayex()) {
  					return false;
  				}
  			} else if($item->product_id == 27) {
				$user = new User($this->user_id);
				if($user->expires == '2011-12-31')
				{
					if(!$user->renewMembershipPayex())
					{
						return false;
					}
				}
				else
					return false;
			}
  		}
	}
}

?>
