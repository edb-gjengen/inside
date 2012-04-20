<?php
require_once('payex.php');

/**
 * Class for using the PayexPxRecurring Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */
class PayexPxRecurring extends Payex
{
	var $recurringRef;
	var $recurringStatus;
	var $paymentMethod;
	var $productNumber;
	var $orderId;

	function setRecurringRef($recurringRef)
	{
		$this->recurringRef = $recurringRef;
	}

	function PayexPxRecurring($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxRecurringWSDL());
	}

	function Start($agreementRef, $startDate, $periodType, $period, $alertPeriod, $price, $productNumber, $orderID, $description, $notifyUrl)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef, $startDate, $periodType, $period, $alertPeriod, $price, $productNumber, $orderID, $description, $notifyUrl);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $agreementRef,
			'startDate' => $startDate,
			'periodType' => $periodType,
			'period' => $period,
			'alertPeriod' => $alertPeriod,
			'price' => $price,
			'productNumber' => $productNumber,
			'orderID' => $orderID,
			'description' => $description,
			'notifyUrl' => $notifyUrl,
			'hash' => $hash
		);

		$return_data = $this->client->Start($params);
		return $this->__processResponse($return_data);
	}

	function Stop($agreementRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $this->agreementRef,
			'hash' => $hash
		);

		$return_data = $this->client->Stop($params);
		return $this->__processResponse($return_data);
	}

	function Check($agreementRef)
	{
		$hash = $this->__generateHash($this->__accountNumber, $agreementRef);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'agreementRef' => $this->agreementRef,
			'hash' => $hash
		);

		$return_data = $this->client->Check($params);
		return $this->__processResponse($return_data);
	}

	function getRecurringStatus()
	{
		return $this->recurringStatus;
	}
	function setRecurringStatus($recurringStatus)
	{
		$this->recurringStatus = $recurringStatus;
	}
	function getPaymentMethod()
	{
		return $this->paymentMethod;
	}
	function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}
	function getProductNumber()
	{
		return $this->productNumber;
	}
	function setProductNumber($productNumber)
	{
		$this->productNumber = $productNumber;
	}
	function getOrderId()
	{
		return $this->orderId;
	}
	function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}
}
?>