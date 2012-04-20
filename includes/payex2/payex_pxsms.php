<?php
require_once('payex.php');

/**
 * Class for using the PayexPxSMS Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 1.0
 *
 */
class PayexPxSMS extends Payex
{
	var $transactionRef;
	var $transactionNumber;
	var $transactionStatus;
	var $orderId;
	var $clientAccount;
	var $paymentMethod;
	var $amount;
	var $stopDate;
	var $productNumber;
	var $remainingCaptureAmount;
	var $remainingCreditAmount;
	
	function PayexPxSMS($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxSMSWSDL());
	}
	
	function SendCpa($orderId, $productNumber, $description, $originatingAddress, $destination, $userData, $dataHeader, $price, $dcs, $validityTime, $deliveryTime)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderId, $productNumber, $description, $originatingAddress, $destination, $userData, $dataHeader, $price, $dcs, $validityTime, $deliveryTime);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderId' => $orderId,
			'productNumber' => $productNumber,
			'description' => $description,
			'originatingAddress' => $originatingAddress,
			'destination' => $destination,
			'userData' => $userData,
			'dataHeader' => $dataHeader,
			'price' => $price,
			'dcs' => $dcs,
			'validityTime' => $validityTime,
			'deliveryTime' => $deliveryTime,
			'hash' => $hash
		);

		$return_data = $this->client->SendCpa($params);
		return $this->__processResponse($return_data);
	}
	
	function SendLa($orderId, $productNumber, $description, $originatingAddress, $addressAlpha, $destination, $userData, $dataHeader, $dcs, $validityTime, $deliveryTime)
	{
		$hash = $this->__generateHash($this->__accountNumber, $orderId, $productNumber, $description, $originatingAddress, $addressAlpha, $destination, $userData, $dataHeader, $dcs, $validityTime, $deliveryTime);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'orderId' => $orderId,
			'productNumber' => $productNumber,
			'description' => $description,
			'originatingAddress' => $originatingAddress,
			'addressAlpha' => $addressAlpha,
			'destination' => $destination,
			'userData' => $userData,
			'dataHeader' => $dataHeader,
			'dcs' => $dcs,
			'validityTime' => $validityTime,
			'deliveryTime' => $deliveryTime,
			'hash' => $hash
		);
		
		$return_data = $this->client->SendLa($params);
		return $this->__processResponse($return_data);
	}
	
	function Check($transactionRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $transactionRef);
		
		$params = array(
			'accountNumber' => $this->__accountNumber,
			'transactionRef' => $transactionRef,
			'hash' => $hash
		);
		
		$return_data = $this->client->Check($params);
		return $this->__processResponse($return_data);
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
	function getOrderId()
	{
		return $this->orderId;
	}

	function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}

	function getClientAccount()
	{
		return $this->clientAccount;
	}

	function setClientAccount($clientAccount)
	{
		$this->clientAccount = $clientAccount;
	}

	function getPaymentMethod()
	{
		return $this->paymentMethod;
	}

	function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}

	function getAmount()
	{
		return $this->amount;
	}

	function setAmount($amount)
	{
		$this->amount = $amount;
	}

	function getStopDate()
	{
		return $this->stopDate;
	}

	function setStopDate($stopDate)
	{
		$this->stopDate = $stopDate;
	}

	function getProductNumber()
	{
		return $this->productNumber;
	}

	function setProductNumber($productNumber)
	{
		$this->productNumber = $productNumber;
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

}
?>