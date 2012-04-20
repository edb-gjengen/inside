<?php

require_once('payex.php');

/**
 * Class for using the PxDeposit Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxDeposit extends Payex
{
	function PayexPxDeposit($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxDepositWSDL());
	}

	function DepositCC($amount, $description, $clientType, $clientRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress, $clientIdentifier1 = '', $clientIdentifier2 = '')
	{
		$hash = $this->__generateHash($this->__accountNumber, $amount, $description, $clientType, $clientRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress, $clientIdentifier1, $clientIdentifier2);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'amount' => $amount,
			'description' => $description,
			'clientType' => $clientType,
			'clientRef' => $clientRef,
			'cardNumber' => $cardNumber,
			'cardNumberExpireMonth' => $cardNumberExpireMonth,
			'cardNumberExpireYear' => $cardNumberExpireYear,
			'cardNumberCVC' => $cardNumberCVC,
			'cardHolderName' => $cardHolderName,
			'clientIPAddress' => $clientIPAddress,
			'clientIdentifier1' => $clientIdentifier1,
			'clientIdentifier2' => $clientIdentifier2,
			'hash' => $hash
		);

		$return_data = $this->client->DepositCC($params);

		return $this->__processResponse($return_data);
	}

	function DepositEVC($description, $clientType, $clientRef, $valueCode, $clientIPAddress = false, $clientIdentifier1, $clientIdentifier2)
	{
		if ( $clientIPAddress === false )
			$clientIPAddress = $_SERVER['REMOTE_ADDR'];

		$hash = $this->__generateHash($this->__accountNumber, $description, $clientType, $clientRef, $valueCode, $clientIPAddress, $clientIdentifier1, $clientIdentifier2);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'description' => $description,
			'clientType' => $clientType,
			'clientRef' => $clientRef,
			'valueCode' => $valueCode,
			'clientIPAddress' => $clientIPAddress,
			'clientIdentifier1' => $clientIdentifier1,
			'clientIdentifier2' => $clientIdentifier2,
			'hash' => $hash
		);

		$return_data = $this->client->DepositEVC($params);

		return $this->__processResponse($return_data);
	}
}
?>