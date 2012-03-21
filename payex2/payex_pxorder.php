<?php
require_once('payex.php');

/**
 * Class for using the PxOrder Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxOrder extends Payex
{
	var $orderRef;
	var $sessionRef;
	var $transactionRef;
	var $transactionNumber;
	var $clientAccount;
	var $amount;
	var $stopDate;
	var $ivrCode;
	var $transactionStatus;
	var $ivrPhoneNumber;
	var $ivrChargeRate;
	var $productNumber;
	var $clientGsmNumber;
	var $orderId;
	var $paymentMethod;
	var $transNumber;
	var $orderStatus;
	var $remainingCaptureAmount;
	var $remainingCreditAmount;
	var $cancelUrl;
	var $agreementRef;
	var $clientIdentifier;
	var $socialSecurityNumber;
	var $email;
	var $firstName;
	var $lastName;
	var $address;
	var $postalCode;
	var $city;
	var $country;
	var $phone1;
	var $phone2;
	var $gsm;
	var $additionalProducts;
	var $shippingDescription;
	var $additionalInfo;
	var $months;
	var $nominalInterest;
	var $pkiMethod;
	var $clientAccountNumber;
	var $password;


	var $__pxOrderURL;


	function PayexPxOrder($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxOrderWSDL());
		$this->__pxOrderURL = $this->__setup->getPxOrderURL();

		$this->orderRef = '';
		$this->sessionRef = '';
		$this->transactionRef = '';
		$this->transactionNumber = '';
		$this->transactionStatus = -1;
		$this->clientAccount = '';
		$this->amount = '';
		$this->stopDate = '';
		$this->ivrPhoneNumber = '';
		$this->ivrChargeRate = '';
		$this->orderId = '';
		$this->paymentMethod = '';
		$this->productNumber = '';
		$this->clientGsmNumber = '';
		$this->transNumber = '';
		$this->ivrCode = '';
		$this->authenticationRequired = false;
		$this->redirectUrl = '';
		$this->orderStatus = 0;
		$this->remainingCaptureAmount = '';
		$this->remainingCreditAmount = '';
		$this->cancelUrl = '';
		$this->agreementRef = '';
		$this->clientIdentifier = '';
	}

	function PrepareAuthorizeCC($orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress, $clientIdentifier)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress, $clientIdentifier);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'cardNumber' => (string) $cardNumber,
			'cardNumberExpireMonth' => (string) $cardNumberExpireMonth,
			'cardNumberExpireYear' => (string) $cardNumberExpireYear,
			'cardNumberCVC' => (string) $cardNumberCVC,
			'cardHolderName' => (string) $cardHolderName,
			'clientIPAddress' => (string) $clientIPAddress,
			'clientIdentifier' => (string) $clientIdentifier,
			'hash' => $hash
			);

			$return_data = $this->client->PrepareAuthorizeCC($params);

			return $this->__processResponse($return_data);
	}
	function PrepareSaleCC($orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress, $clientIdentifier)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress, $clientIdentifier);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'cardNumber' => (string) $cardNumber,
			'cardNumberExpireMonth' => (string) $cardNumberExpireMonth,
			'cardNumberExpireYear' => (string) $cardNumberExpireYear,
			'cardNumberCVC' => (string) $cardNumberCVC,
			'cardHolderName' => (string) $cardHolderName,
			'clientIPAddress' => (string) $clientIPAddress,
			'clientIdentifier' => (string) $clientIdentifier,
			'hash' => $hash
			);

			$return_data = $this->client->PrepareSaleCC($params);

			return $this->__processResponse($return_data);
	}

	function PrepareSaleDD($orderRef, $userType, $userRef, $bankName, $clientIPAddress = false, $clientIdentifier)
	{
		if ( $clientIPAddress === false )
		$clientIPAddress = $_SERVER['REMOTE_ADDR'];

		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $bankName, $clientIPAddress, $clientIdentifier);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'bankName' => (string) $bankName,
			'clientIPAddress' => (string) $clientIPAddress,
			'clientIdentifier' => (string) $clientIdentifier,
			'hash' => $hash
			);

			$return_data = $this->client->PrepareSaleDD($params);

			return $this->__processResponse($return_data);
	}

	function Initialize3($purchaseOperation, $orderType, $period, $price, $priceArgList, $vat, $orderID, $productNumber, $description, $clientIPAddress, $externalID, $returnUrl, $view, $viewType)
	{
		$hash = $this->__generateHash($this->__accountNumber, $purchaseOperation, $orderType, $period, $price, $priceArgList, $vat, $orderID, $productNumber, $clientIPAddress, $externalID, $returnUrl, $view, $viewType);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'purchaseOperation' => $purchaseOperation,
			'orderType' => $orderType,
			'period' => $period,
			'price' => $price,
			'priceArgList' => $priceArgList,
			'vat' => $vat,
			'orderID' => $orderID,
			'productNumber' => $productNumber,
			'description' => $description,
			'clientIPAddress' => $clientIPAddress,
			'externalID' => $externalID,
			'returnUrl' => $returnUrl,
			'view' => $view,
			'viewType' => $viewType,
			'hash' => $hash
			);

			$return_data = $this->client->Initialize3($params);

			return $this->__processResponse($return_data);
	}

	function Initialize4($purchaseOperation, $price, $priceArgList, $currency, $vat, $orderID, $productNumber, $description, $clientIPAddress, $externalID, $returnUrl, $view, $viewType, $agreementRef, $cancelUrl)
	{
		$hash = $this->__generateHash($this->__accountNumber, $purchaseOperation, $price, $priceArgList, $currency, $vat, $orderID,  $productNumber, $description, $clientIPAddress, $externalID, $returnUrl, $view, $viewType, $agreementRef, $cancelUrl);

		$params = array(
 			'accountNumber' => $this->__accountNumber,
 			'purchaseOperation' => $purchaseOperation,
 			'price' => $price,
 			'priceArgList' => $priceArgList,
 			'currency' => $currency,
 			'vat' => $vat,
 			'orderID' => $orderID,
 			'productNumber' => $productNumber,
 			'description' => $description,
 			'clientIPAddress' => $clientIPAddress,
 			'externalID' => $externalID,
 			'returnUrl' => $returnUrl,
 			'view' => $view,
 			'viewType' => $viewType,
 			'agreementRef' => $agreementRef,
 			'cancelUrl' => $cancelUrl,
 			'hash' => $hash
 			);

 			$return_data = $this->client->Initialize4($params);

 			return $this->__processResponse($return_data);
	}

	function Initialize5($purchaseOperation, $price, $priceArgList, $currency, $vat, $orderID, $productNumber, $description, $clientIPAddress, $clientIdentifier, $externalID, $returnUrl, $view, $viewType, $agreementRef, $cancelUrl)
	{
		$hash = $this->__generateHash($this->__accountNumber, $purchaseOperation, $price, $priceArgList, $currency, $vat, $orderID,  $productNumber, $description, $clientIPAddress, $clientIdentifier, $externalID, $returnUrl, $view, $viewType, $agreementRef, $cancelUrl);

		$params = array(
 			'accountNumber' => $this->__accountNumber,
 			'purchaseOperation' => $purchaseOperation,
 			'price' => $price,
 			'priceArgList' => $priceArgList,
 			'currency' => $currency,
 			'vat' => $vat,
 			'orderID' => $orderID,
 			'productNumber' => $productNumber,
 			'description' => $description,
 			'clientIPAddress' => $clientIPAddress,
 			'clientIdentifier' => $clientIdentifier,
 			'externalID' => $externalID,
 			'returnUrl' => $returnUrl,
 			'view' => $view,
 			'viewType' => $viewType,
 			'agreementRef' => $agreementRef,
 			'cancelUrl' => $cancelUrl,
 			'hash' => $hash
 			);

 			$return_data = $this->client->Initialize5($params);

 			return $this->__processResponse($return_data);
	}
	
	function InitializeBasic($purchaseOperation, $orderID, $productNumber, $priceArgList, $description, $returnURL)
	{
		$hash = $this->__generateHash($this->__accountNumber, $purchaseOperation, $orderID, $productNumber, $priceArgList, $returnURL);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'purchaseOperation' => (string) $purchaseOperation,
			'orderID' => (string) $orderID,
			'productNumber' => (string) $productNumber,
			'priceArgList' => (string) $priceArgList,
			'description' => (string) $description,
			'returnURL' => (string) $returnURL,
			'hash' => $hash
			);

			$return_data = $this->client->InitializeBasic($params);

			return $this->__processResponse($return_data);
	}

	function ReserveIVR($orderRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef', (string) $orderRef,
			'hash' => $hash
			);


			$return_data = $this->client->ReserveIVR($params);
			return $this->__processResponse($return_data);
	}

	function SaleIVR($orderRef, $userType, $userRef, $ivrCode)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $ivrCode);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'userType' => $userType,
			'userRef' => $userRef,
			'ivrCode' => $ivrCode,
			'hash' => $hash
			);

			$return_data = $this->client->SaleIVR($params);
			return $this->__processResponse($return_data);
	}

	function SaleEVC($orderRef, $userType, $userRef, $valueCode, $clientIPAddress = false)
	{
		if ( $clientIPAddress === false )
		$clientIPAddress = $_SERVER['REMOTE_ADDR'];

		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $valueCode, $clientIPAddress);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'userType' => $userType,
			'userRef' => $userRef,
			'valueCode' => $valueCode,
			'clientIPAddress' => $clientIPAddress,
			'hash' => $hash
			);

			$return_data = $this->client->SaleEVC($params);
			return $this->__processResponse($return_data);
	}

	function AddLoan($orderRef, $socialSecurityNumber, $email, $firstName, $lastName, $address, $postalCode, $city, $country, $phone1, $phone2, $gsm, $additionalProducts, $shippingDescription, $additionalInfo, $months, $nominalInterest, $pkiMethod)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $socialSecurityNumber, $email, $firstName, $lastName, $address, $postalCode, $city, $country, $phone1, $phone2, $gsm, $additionalProducts, $shippingDescription, $additionalInfo, $months, $nominalInterest, $pkiMethod);

		$params = array(
		'accountNumber' => $this->__accountNumber,
		'orderRef' => $orderRef, 
		'socialSecurityNunber' => $socialSecurityNumber, 
		'email' => $email, 
		'firstName' => $firstName, 
		'lastName' => $lastName, 
		'address' => $address, 
		'postalCode' => $postalCode, 
		'city' => $city, 
		'country' => $country, 
		'phone1' => $phone1, 
		'phone2' => $phone2, 
		'gsm' => $gsm, 
		'additionalProducts' => $additionalProducts, 
		'shippingDescription' => $shippingDescription, 
		'additionalInfo' => $additionalInfo, 
		'months' => $months, 
		'nominalInterest' => $nominalInterest,
		'pkiMethod' => $pkiMethod,
		'hash' => $hash
		);

		$return_data = $this->client->AddLoan($params);
		return $this->__processResponse($return_data);
	}
	
	function AuthorizeCA($orderRef, $clientAccountNumber, $password)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $clientAccountNumber, $password);

		$params = array(
		'accountNumber' => $this->__accountNumber, 
		'orderRef' => $orderRef, 
		'clientAccountNumber' => $clientAccountNumber, 
		'password' => $password,
		'hash' => $hash);

		$return_data = $this->client->AuthorizeCA($params);
		return $this->__processResponse($return_data);
	}
	
	function SaleCA($orderRef, $clientAccountNumber, $password)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $clientAccountNumber, $password);

		$params = array(
		'accountNumber' => $this->__accountNumber, 
		'orderRef' => $orderRef, 
		'clientAccountNumber' => $clientAccountNumber, 
		'password' => $password,
		'hash' => $hash
		);

		$return_data = $this->client->SaleCA($params);
		return $this->__processResponse($return_data);
	}
	
	function AuthorizeEVC($orderRef, $userType, $userRef, $valueCode, $clientIPAddress = false)
	{
		if ( $clientIPAddress === false )
		$clientIPAddress = $_SERVER['REMOTE_ADDR'];

		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $valueCode, $clientIPAddress);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'userType' => $userType,
			'userRef' => $userRef,
			'valueCode' => $valueCode,
			'clientIPAddress' => $clientIPAddress,
			'hash' => $hash
			);

			$return_data = $this->client->AuthorizeEVC($params);
			return $this->__processResponse($return_data);
	}
	
	function AddSingleOrderLine($orderRef, $itemNumber, $itemDescription1, $itemDescription2, $itemDescription3, $itemDescription4, $itemDescription5, $quantity, $amount, $vatPrice, $vatPercent)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $itemNumber, $itemDescription1, $itemDescription2, $itemDescription3, $itemDescription4, $itemDescription5, $quantity, $amount, $vatPrice, $vatPercent);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'itemNumber' => $itemNumber,
			'itemDescription1' => $itemDescription1,
			'itemDescription2' => $itemDescription2,
			'itemDescription3' => $itemDescription3,
			'itemDescription4' => $itemDescription4,
			'itemDescription5' => $itemDescription5,
			'quantity' => $quantity,
			'amount' => $amount,
			'vatPrice' => $vatPrice,
			'vatPercent' => $vatPercent,
			'hash' => $hash
			);

			$return_data = $this->client->AddSingleOrderLine($params);
			return $this->__processResponse($return_data);
	}

	function AddMultipleOrderLines($orderRef, $numberOfItems, $orderDetails)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $numberOfItems);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => $orderRef,
			'numberOfItems' => $numberOfItems,
			'orderDetails' => $orderDetails,
			'hash' => $hash
			);

			$return_data = $this->client->AddMultipleOrderLines($params);
			return $this->__processResponse($return_data);
	}


	function Initialize($orderType, $purchaseOperation, $autoRenew, $view, $period, $orderID, $productNumber, $subscriptionNumber, $price, $priceArgList, $vat, $description, $returnURL, $checkPeriod, $externalID, $initialDiscount, $expireDate, $expireDateType)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderType, $purchaseOperation, $autoRenew, $view, $period, $orderID, $productNumber, $subscriptionNumber, $price, $priceArgList, $vat, $returnURL, $checkPeriod, $initialDiscount, $expireDate, $expireDateType);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderType' => (string) $orderType,
			'purchaseOperation' => (string) $purchaseOperation,
			'autoRenew' => (int) $autoRenew,
			'view' => (string) $view,
			'period' => (int) $period,
			'orderID' => (string) $orderID,
			'productNumber' => (string) $productNumber,
			'subscriptionNumber' => (string) $subscriptionNumber,
			'price' => (int) $price,
			'priceArgList' => (string) $priceArgList,
			'vat' => (int) $vat,
			'description' => (string) $description,
			'returnURL' => (string) $returnURL,
			'checkPeriod' => (int) $checkPeriod,
			'externalID' => (string) $externalID,
			'initialDiscount' => (int) $initialDiscount,
			'expireDate' => (string) $expireDate,
			'expireDateType' => (int) $expireDateType,
			'hash' => $hash
			);

			$return_data = $this->client->Initialize($params);

			return $this->__processResponse($return_data);
	}

	function Complete($orderRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'hash' => $hash
			);

			$return_data = $this->client->Complete($params);
			//return $this->__processResponse($return_data);
			$tmp = $this->__processResponse($return_data);
			
/* Comment out the following lins for transaction response debugging (nikolark) */
//$data = "\n\n\n". time() . "\n\n" . var_export($return_data, true) . "\ntmp\n" . var_export($tmp, true);
//file_put_contents('tmp', $data, FILE_APPEND);

			return $tmp;
	}

	function Check($transactionRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $transactionRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'transactionRef' => (string) $transactionRef,
			'hash' => $hash
			);

			$return_data = $this->client->Check($params);

			return $this->__processResponse($return_data);
	}

	function SalePX($orderRef, $userType, $userRef, $password)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $password);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'password' => (string) $password,
			'hash' => $hash
			);

			$return_data = $this->client->SalePX($params);

			return $this->__processResponse($return_data);
	}

	function AuthorizePX($orderRef, $userType, $userRef, $password)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $password);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'password' => (string) $password,
			'hash' => $hash
			);

			$return_data = $this->client->AuthorizePX($params);

			return $this->__processResponse($return_data);
	}

	function SaleCC($orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'cardNumber' => (string) $cardNumber,
			'cardNumberExpireMonth' => (string) $cardNumberExpireMonth,
			'cardNumberExpireYear' => (string) $cardNumberExpireYear,
			'cardNumberCVC' => (string) $cardNumberCVC,
			'hash' => $hash
			);

			$return_data = $this->client->SaleCC($params);

			return $this->__processResponse($return_data);
	}

	function AuthorizeCC($orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'cardNumber' => (string) $cardNumber,
			'cardNumberExpireMonth' => (string) $cardNumberExpireMonth,
			'cardNumberExpireYear' => (string) $cardNumberExpireYear,
			'cardNumberCVC' => (string) $cardNumberCVC,
			'hash' => $hash
			);

			$return_data = $this->client->AuthorizeCC($params);

			return $this->__processResponse($return_data);
	}

	function Capture($transactionRef, $amount)
	{
		$hash = $this->__generateHash($this->__accountNumber, $transactionRef, $amount);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'transactionRef' => (string) $transactionRef,
			'amount' => (int) $amount,
			'hash' => $hash
			);

			$return_data = $this->client->Capture($params);

			return $this->__processResponse($return_data);
	}

	function Credit($transactionRef, $amount)
	{
		$hash = $this->__generateHash($this->__accountNumber, $transactionRef, $amount);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'transactionRef' => (string) $transactionRef,
			'amount' => (int) $amount,
			'hash' => $hash
			);

			$return_data = $this->client->Credit($params);

			return $this->__processResponse($return_data);
	}

	function Cancel($transactionRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $transactionRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'transactionRef' => (string) $transactionRef,
			'hash' => $hash
			);

			$return_data = $this->client->Cancel($params);

			return $this->__processResponse($return_data);
	}

	function SaleCPA($orderRef, $userType, $userRef, $password)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $password);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'userType' => (string) $userType,
			'userRef' => (string) $userRef,
			'password' => (string) $password,
			'hash' => $hash
			);

			$return_data = $this->client->SaleCPA($params);

			return $this->__processResponse($return_data);
	}

	function SaleInvoice($orderRef, $userType, $userRef, $invoiceText, $mediaDistribution, $customerId, $customerName, $customerPostNumber, $customerCity, $customerCountry, $customerSocialSecurityNumber, $customerPhoneNumber, $customerEmailAddress, $customerCOAddress, $customerStreetAddress, $productCode, $dueDateExceeded)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderRef, $userType, $userRef, $invoiceText, $mediaDistribution, $customerId, $customerName, $customerPostNumber, $customerCity, $customerCountry,
		$customerSocialSecurityNumber, $customerPhoneNumber, $customerEmailAddress, $customerCOAddress, $customerStreetAddress, $productCode, $dueDateExceeded);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderRef' => (string) $orderRef,
			'userType' => (string) $userType,
			'userRef' => (string) $userRef,
			'invoiceText' => (string) $invoiceText,
			'mediaDistribution' => (string) $mediaDistribution,
			'customerId' => (string) $customerId,
			'customerName' => (string) $customerName,
			'customerPostNumber' => (string) $customerPostNumber,
			'customerCity' => (string) $customerCity,
			'customerCountry' => (string) $customerCountry,
			'customerSocialSecurityNumber' => (string) $customerSocialSecurityNumber,
			'customerPhoneNumber' => (string) $customerPhoneNumber,
			'customerEmailAddress' => (string) $customerEmailAddress,
			'customerCOAddress' => (string) $customerCOAddress,
			'customerStreetAddress' => (string) $customerStreetAddress,
			'productCode' => (string) $productCode,
			'dueDateExceeded' => (string) $dueDateExceeded,
			'hash' => $hash
			);

			$return_data = $this->client->SaleInvoice($params);

			return $this->__processResponse($return_data);
	}

	function redirect($orderRef, $showMenu = true)
	{
		$orderRef = (string) $orderRef;

		$link = $this->__pxOrderURL.'?OrderRef='.urlencode($orderRef).'&MENU='.($showMenu?'1':'0');

		if ( headers_sent() )
		echo '<script type="text/javascript">location = \''.htmlspecialchars($link).'\';</script>';
		else
		header('Location: '.$link);
	}


	function getRemainingCaptureAmount()
	{
		return $this->remainingCaptureAmount;
	}
	function setRemainingCaptureAmount($remainingCaptureAmount)
	{
		$this->remainingCaptureAmount = $remainingCaptureAmount;
	}
	function getRemainingCreditAmount()
	{
		return $this->remainingCreditAmount;
	}
	function setRemainingCreditAmount($remainingCreditAmount)
	{
		$this->remainingCreditAmount = $remainingCreditAmount;
	}
	function setIvrChargeRate($ivrChargeRate)
	{
		$this->ivrChargeRate = $ivrChargeRate;
	}
	function getIvrChargeRate()
	{
		return $this->ivrChargeRate;
	}
	function setIvrPhoneNumber($ivrPhoneNumber)
	{
		$this->ivrPhoneNumber = $ivrPhoneNumber;
	}
	function getIvrPhoneNumber()
	{
		return $this->ivrPhoneNumber;
	}
	function getOrderRef()
	{
		return $this->orderRef;
	}
	function getSessionRef()
	{
		return $this->sessionRef;
	}
	function getTransactionNumber()
	{
		return $this->transactionNumber;
	}
	function getTransactionRef()
	{
		return $this->transactionRef;
	}
	function getTransactionStatus()
	{
		return $this->transactionStatus;
	}
	function getStopDate()
	{
		return $this->stopDate;
	}
	function getClientAccount()
	{
		return $this->clientAccount;
	}
	function getAmount()
	{
		return $this->amount;
	}
	function getIvrCode()
	{
		return $this->ivrCode;
	}
	function setIvrCode($ivrCode)
	{
		$this->ivrCode = $ivrCode;
	}
	function setOrderRef($orderRef)
	{
		$this->orderRef = $orderRef;
	}
	function setSessionRef($sessionRef)
	{
		$this->sessionRef = $sessionRef;
	}
	function setTransactionNumber($transactionNumber)
	{
		$this->transactionNumber = $transactionNumber;
	}
	function setTransactionRef($transactionRef)
	{
		$this->transactionRef = $transactionRef;
	}
	function setTransactionStatus($transactionStatus)
	{
		$this->transactionStatus = (int) $transactionStatus;
	}
	function setStopDate($stopDate)
	{
		$this->stopDate = $stopDate;
	}
	function setClientAccount($clientAccount)
	{
		$this->clientAccount = $clientAccount;
	}
	function setAmount($amount)
	{
		$this->amount = (int) $amount;
	}
	function getProductNumber()
	{
		return $this->productNumber;
	}
	function setProductNumber($productNumber)
	{
		$this->productNumber = $productNumber;
	}
	function getClientGsmNumber()
	{
		return $this->clientGsmNumber;
	}
	function setClientGsmNumber($clientGsmNumber)
	{
		$this->clientGsmNumber = $clientGsmNumber;
	}
	function getPaymentMethod()
	{
		return $this->paymentMethod;
	}
	function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}
	function getOrderId()
	{
		return $this->orderId;
	}
	function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}
	function getTransNumber()
	{
		return $this->transNumber;
	}
	function setTransNumber($transNumber)
	{
		$this->transNumber = $transNumber;
	}
	function setAuthenticationRequired($required)
	{
		if(strtolower((string) $required) == 'true')
		$this->authenticationRequired = true;
		else
		$this->authenticationRequired = false;
	}
	function getAuthenticationRequired()
	{
		return $this->authenticationRequired;
	}
	function setRedirectUrl($url)
	{
		$this->redirectUrl = (string) $url;
	}
	function getRedirectUrl()
	{
		return $this->redirectUrl;
	}
	function getOrderStatus()
	{
		return $this->orderStatus;
	}
	function setOrderStatus($status)
	{
		$this->orderStatus = (int) $status;
	}
}
?>
