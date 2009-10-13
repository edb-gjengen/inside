<?php


/*
 *This class requires PEAR::DB and functions library
 */
class Payment {

	var $id;
	var $user_email;
	var $payex;

	public
	function __construct() {
		$this->initPayex();
	}
	
	private function initPayex() {
		//From setupexample
		define('PAYEX_PXPARTNER_WSDL', 'https://external.payex.com/pxorder/pxpartner.asmx?WSDL'); 
		define('PAYEX_PXPRINT_WSDL', 'https://external.payex.com/pxorder/pxprint.asmx?WSDL'); 
		define('PAYEX_PXDEPOSIT_WSDL', 'https://external.payex.com/pxorder/pxdeposit.asmx?WSDL');
		define('PAYEX_PXACCOUNT_WSDL', 'https://external.payex.com/pxorder/pxaccount.asmx?WSDL');
		define('PAYEX_PXORDER_WSDL', 'https://external.payex.com/pxorder/pxorder.asmx?WSDL');
		define('PAYEX_PXCLIENT_WSDL', 'https://external.payex.com/pxclient/pxclient.asmx?WSDL'); 
		define('PAYEX_PXMERCHANT_WSDL', 'https://external.payex.com/pxmerchant/pxmerchant.asmx?WSDL');
		define('PAYEX_PXSUBSCRIPTION_WSDL', 'https://external.payex.com/pxsubscription/pxsubscription.asmx?WSDL');
		define('PAYEX_PXAGREEMENT_WSDL', 'https://external.payex.com/pxagreement/pxagreement.asmx?WSDL'); 
		
		define('PAYEX_PXORDER_URL', 'https://account.payex.com/pxorder.asp'); // url to redirect user to
		define('PAYEX_ACCOUNTNUMBER', 20180218); // payex merchant account number
		define('PAYEX_ENCRYPTIONKEY', 'vy8wMAvJjvW4RaCEWJFf'); // payex encryption key
		// end setup
		require_once ('../payex2/payex_pxorder.php'); // Include the Payex PxOrder implementation
		$this->payex = new PayexPxOrder(); // Make an instance of the PayexPxOrder class
		
		$this->user_email = false;
	  
	}
	
	public function executeRedirect($transaction_id_string, $order_id){
		//init order
		$order = new Order($order_id);
		$this->amount = $order->calculateTotalAmount();
		if (Transaction :: isFreeIdString($transaction_id_string)) {
			$trans_data = Array("id_string" => $transaction_id_string,
													"user_id" => getCurrentUser(), 
													"order_id" => $order_id,
													"status" => "PENDING",
													"amount" => $this->amount);
			$transaction = new Transaction(NULL, $trans_data);
			$transaction->store();
			$transaction_id = $transaction->id;
		}else {
			notify("Transaksjonen er allerede forsøkt gjennomført. Dette skyldes sannsynligvis at du har trykket to ganger på 'submit'-knappen.");
			return;	
		}

		
		// set the parameters for Initialize
		$purchaseOperation = 'SALE'; // 1-phased transaction
		$orderID = $transaction->id; //Order id, generated from database
		$productNumber = 'Handlevogn #'.$order->id; //Order id
		$price = $this->amount * 100;
		$priceArgList = 'VISA=%s,MC=%s'; // No CPA, VISA, MasterCard, SMS
		$priceArgList = sprintf($priceArgList, $price, $price);
		$description = "Kjøp fra Studentersamfundet.no";
		$returnURL = 'https://www.studentersamfundet.no/inside/index.php?action=transaction-return&transactionid='.$transaction_id; // ReturnURL

		// Run Initialize. Check return-value
		if ( $this->payex->InitializeBasic(
						$purchaseOperation, $orderID, $productNumber, $priceArgList, 
						$description, $returnURL) ) {
			$orderRef = $this->payex->getOrderRef(); // get the orderRef
			$this->payex->redirect($orderRef); // redirect the user
		} else {
			echo 'Initialize() failed.<br>';
			echo 'Code: '.$this->payex->getcode().' ';
			echo 'Description: '. $this->payex->getdescription().'<br>';
		}
		return false;	
	}
	
	public function completeTransaction($orderRef, $transaction_id) {
		if ( $this->payex->Complete($orderRef) ) {
			
				/*
				Transaction statuses (defined in payex_defines.php):
				0=Sale, 1=Initialize, 2=Credit, 3=Authorize, 4=Cancel, 5=Failure, 6=Capture
				*/
			//notify($this->payex->getTransactionStatus());
			if (!($this->payex->getTransactionStatus() == PAYEX_TRANSACTIONSTATUS_SALE || 
				$this->payex->getTransactionStatus() == PAYEX_TRANSACTIONSTATUS_CAPTURE) )
			{
				notify("Betalingen er ikke riktig gjennomført. Vennligst ta kontakt med support om du tror dette skyldes en feil.");
				return false;
			}
			
			$transaction = new Transaction($transaction_id);
			$transaction->setStatus("OK");
			notify("Betalingen er gjennomført. En kvittering er blitt sendt til din registrerte epostadresse.");
			if (!$this->user_email) { // get user mail from current user
  			$user = new User(getCurrentUser());
	   		$this->user_email = $user->email;
			}
			$this->_sendConfirmationMail($transaction_id);
			return true;
		}else {
			notify("Betalingen er ikke riktig gjennomført. Vennligst ta kontakt med support om du tror dette skyldes en feil.");
			return false;
		}
	}
	
	public function setUserEmail ($newmail) {
	  $this->user_email = $newmail;
	}

    public function _sendConfirmationMail ($transaction_id) {
        $sendto = $this->user_email;
        $transaction = new Transaction($transaction_id);
        $order = new Order($transaction->order_id);
        $subject = "=?iso8859-1?Q?[$transaction->id] Kvittering for kj=F8p p=E5 studentersamfundet.no?=";
        $message = "Hei!\n\n" .
            "Du har nettopp gjennomført et kjøp på studentersamfundet.no. Transaksjonen har referansen $transaction->id.\n\n" .
            "Du har kjøpt:\n" .
            $order->getConfirmationText()."\n" .
            "Prisen for dette er NOK $transaction->amount,-\n" .
            "Beløpet er blitt belastet ditt VISA-kort. På kontoutskriften vil det stå Payex AS.\n\n" .
            "For spørsmål angående dette kan du svare på denne eposten. Se forøvrig http://www.studentersamfundet.no/kontakt.php for ytterligere kontaktinformasjon.\n\n" . 
            "Med vennlig hilsen\n" .
            "Det Norske Studentersamfund\n\n";
        $headers = "From: Det Norske Studentersamfund <support@studentersamfundet.no>\r\n";

        mail($sendto, $subject, $message, $headers);
	}

}
?>