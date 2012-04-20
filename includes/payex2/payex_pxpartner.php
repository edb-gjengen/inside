<?php

require_once('payex.php');

/**
 * Class for using the PxPartner Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxPartner extends Payex
{
	var $ivrCode;
	var $transactionStatus;
	var $transactionRef;
	var $transactionNumber;

	function PayexPxPartner($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxPartnerWSDL());

		$this->ivrCode = '';
		$this->transactionStatus = '';
		$this->transactionRef = '';
		$this->transactionNumber = '';
	}

	function ReserveIVRCode($identifierRef, $ivrPhoneNumber, $clientPhoneNumber)
	{
		$hash = $this->__generateHash($this->__accountNumber, $identifierRef, $ivrPhoneNumber, $clientPhoneNumber);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'identifierRef' => $identifierRef,
			'ivrPhoneNumber' => $ivrPhoneNumber,
			'clientPhoneNumber' => $clientPhoneNumber,
			'hash' => $hash
		);

		$return_data = $this->client->ReserveIVRCode($params);

		return $this->__processResponse($return_data);
	}

	function IssueReservedIVRCode($identifierRef, $ivrPhoneNumber, $clientPhoneNumber, $ivrCode)
	{
		$hash = $this->__generateHash($this->__accountNumber, $identifierRef, $ivrPhoneNumber, $clientPhoneNumber, $ivrCode);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'identifierRef' => $identifierRef,
			'ivrPhoneNumber' => $ivrPhoneNumber,
			'clientPhoneNumber' => $clientPhoneNumber,
			'ivrCode' => $ivrCode,
			'hash' => $hash
		);

		$return_data = $this->client->IssueReservedIVRCode($params);

		return $this->__processResponse($return_data);
	}

	function IsValidIVRCode($ivrCode)
	{
		$hash = $this->__generateHash($this->__accountNumber, $ivrCode);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'ivrCode' => $ivrCode,
			'hash' => $hash
		);

		$return_data = $this->client->IsValidIVRCode($params);
		return $this->__processResponse($return_data);
	}

	function getIvrCode()
	{
		return $this->ivrCode;
	}
	function setIvrCode($ivrCode)
	{
		$this->ivrCode = $ivrCode;
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
}
?>