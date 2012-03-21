<?php
require_once('payex.php');

/**
 * Class for using the PayexPxAgreement Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.5
 *
 */
class PayexPxAgreement extends Payex
{
	var $agreementRef;
	var $transactionStatus;
	var $transactionRef;
	var $transactionNumber;
	var $paymentMethod;

	function PayexPxAgreement($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxAgreementWSDL());
	}

	function CreateAgreement($clientType, $clientRef, $merchantRef, $description, $paymentMethod, $maxAmount, $notifyUrl, $startDate = '', $stopDate = '')
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $merchantRef, $description, $paymentMethod, $maxAmount, $notifyUrl);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => $clientType,
			'clientRef' => $clientRef,
			'merchantRef' => $merchantRef,
			'description' => $description,
			'paymentMethod' => $paymentMethod,
			'maxAmount' => $maxAmount,
			'notifyUrl' => $notifyUrl,
			'startDate' => $startDate,
			'stopDate' => $stopDate,
			'hash' => $hash
		);

		$return_data = $this->client->CreateAgreement($params);
		return $this->__processResponse($return_data);
	}
	
	function AddCreditCard($agreementRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $agreementRef,
			'cardNumber' => $cardNumber,
			'cardNumberExpireMonth' => $cardNumberExpireMonth,
			'cardNumberExpireYear' => $cardNumberExpireYear,
			'cardNumberCVC' => $cardNumberCVC,
			'cardHolderName' => $cardHolderName,
			'clientIPAddress' => $clientIPAddress,
			'hash' => $hash
		);

		$return_data = $this->client->AddCreditCard($params);
		return $this->__processResponse($return_data);
	}
	
	function DeleteAgreement($agreementRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $agreementRef,
			'hash' => $hash
		);

		$return_data = $this->client->DeleteAgreement($params);
		return $this->__processResponse($return_data);
	}

	function Alert($agreementRef, $price, $billingDate, $lateWarningDate)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef, $price, $billingDate, $lateWarningDate);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $agreementRef,
			'price' => $price,
			'billingDate' => $billingDate,
			'lateWarningDate' => $lateWarningDate,
			'hash' => $hash
		);

		$return_data = $this->client->Alert($params);
		return $this->__processResponse($return_data);
	}

	function AutoPay($agreementRef, $price, $productNumber, $description, $orderId)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef, $price, $productNumber, $description, $orderId);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $agreementRef,
			'price' => $price,
			'productNumber' => $productNumber,
			'description' => $description,
			'orderId' => $orderId,
			'hash' => $hash
		);

		$return_data = $this->client->AutoPay($params);
		return $this->__processResponse($return_data);
	}

	function getAgreementRef()
	{
		return $this->agreementRef;
	}
	function setAgreementRef($agreementRef)
	{
		$this->agreementRef = $agreementRef;
	}
	function getTransactionStatus()
	{
		return $this->transactionStatus;
	}
	function setTransactionStatus($transactionStatus)
	{
		$this->transactionStatus = $transactionStatus;
	}
	function getTransactionRef()
	{
		return $this->transactionRef;
	}
	function setTransactionRef($transactionRef)
	{
		$this->transactionRef = $transactionRef;
	}
	function getTransactionNumber()
	{
		return $this->transactionNumber;
	}
	function setTransactionNumber($transactionNumber)
	{
		$this->transactionNumber = $transactionNumber;
	}
	function getPaymentMethod()
	{
		return $this->paymentMethod;
	}
	function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}
}
?>