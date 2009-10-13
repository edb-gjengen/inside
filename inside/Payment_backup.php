<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Payment {

	var $id;
	var $conn;
	var $user_email;
	var $status;
	var $transaction_id;

	function Payment($id = NULL, $data = NULL) {
		$this->__construct($id, $data);
	}

	public
	function __construct($id = NULL, $data = NULL) {
		$this->conn = db_connect();

		$this->id = $id;
		$this->status = "FAILURE";
	
		$product = new Product($data['productid']);
		if (Transaction :: isFreeIdString($data['transactionid'])) {
			$trans_data = Array("id_string" => $data['transactionid'],
													"user_id" => getCurrentUser(), 
													"product_id" => $product->id,
													"status" => "PENDING");
			$transaction = new Transaction(NULL, $trans_data);
			$transaction->store();
		}else {
			notify("Transaksjonen er allerede gjennomført. Dette skyldes sannsynligvis at du har trykket to ganger på 'submit'-knappen.");
			return;	
		}

		$amount = $product->price * 100;

		require_once ('../payex/payex_pxorder.php'); // Include the Payex PxOrder implementation

		$payex = new PayexPxOrder(); // Make an instance of the PayexPxOrder class

		$enc_key = PAYEX_ENCRYPTIONKEY;

		$accountNumber = PAYEX_ACCOUNTNUMBER; //Our merchant id
		$purchaseOperation = "SALE"; //Type of pruchase, always SALE
		$orderID = $transaction->id; //Order id, generated from database
		
		$productNumber = $product->id; //Product id
		$priceArgList = "VISA=$amount"; //Price list, as of now only VISA is supported
		$descripion = $product->description;
		$returnURL = "https://www.studentersamfundet.no/inside/index.php?page=payex-form";

		$payex->InitializeBasic($purchaseOperation, $orderID, $productNumber, $priceArgList, $descripion, $returnURL);

		if ($payex->code == 'OK') {
			//notify("Kontakt med betalingsserver er etablert.");
		}else {
			notify("Problemer med å oppnå kontakt med betalingsserver, vennligst prøv igjen senere.");
			return;
		}
		
		$orderRef = $payex->orderRef;
		$userType = PAYEX_USERTYPE_EMAIL;
		$user = new User(getCurrentUser());
		$userRef = $user->email;
		$cardNumber = $data['cardNumber']; //test: "4925000000000004";
		$cardNumberExpireMonth = $data['cardNumberExpireMonth']; //test: "08";
		$cardNumberExpireYear = $data['cardNumberExpireYear']; //test: "08";
		$cardNumberCVC = $data['cardNumberCVC']; //test: "123";

		//SaleCC for direct creditcard
		$payex->SaleCC($orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC);

		if ($payex->code == 'OK') {
			$this->status = "OK";
			$this->transaction_id = $transaction->id;
			$transaction->setStatus("OK");
			notify("Betalingen er gjennomført. En kvittering er blitt sendt til din registrerte epostadresse.");
			$this->user_email = $userRef;
			$this->_sendConfirmationMail();
		}else {
			$transaction->setStatus("FAILURE");
			error("Teknisk kode: " . $payex->getCode() . " - ". $payex->getDescription());
			notify("Problemer med betaling. Vennligst påse at du har tastet inn riktig VISA-informasjon.");
			return;
		}
	}

	public
	function _sendConfirmationMail () {
		$sendto = $this->user_email;
		$transaction = new Transaction($this->transaction_id);
		$product = new Product($transaction->product_id);
  	$subject = "[$transaction->id]Kvittering for kjøp på studentersamfundet.no";
  	$message = "Hei!\n\n" .
  						 "Du har nettopp gjennomført et kjøp på studentersamfundet.no. Ordren har referansen $transaction->id.\n\n" .
  						 "Du har kjøpt:\n" .
  						 "$product->title\n" .
  						 "$product->description\n\n" .
  						 "Prisen for dette er NOK $product->price,-\n" .
  						 "Beløpet er blitt belastet ditt VISA-kort. På kontoutskriften vil det stå Payex AS.\n\n" .
  						 "For spørsmål angående dette kan du svare på denne eposten. Se forøvrig http://www.studentersamfundet.no/kontakt.php for ytterligere kontaktinformasjon.\n\n";
  	$headers = 'From: Det Norske Studentersamfund <support@studentersamfundet.no>'."\r\n";
  	mail($sendto, $subject, $message, $headers);
	}

}
?>