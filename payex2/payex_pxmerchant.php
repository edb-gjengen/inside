<?php
require_once('payex.php');

/**
 * Class for using the PxMerchant Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxMerchant extends Payex
{
	var $transactionRef;
	var $transactionNumber;
	var $MD5Hash;

	function PayexPxMerchant($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxMerchantWSDL());
	}

	function TransferToClient($userType, $userRef, $amount, $description)
	{
		$hash = $this->__generateHash($this->__accountNumber, $userType, $userRef, $amount);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'amount' => (int) $amount,
			'description' => (string) $description,
			'hash' => $hash
		);

		$return_data = $this->client->TransferToClient($params);

		return $this->__processResponse($return_data);
	}

	function GenerateHash($string)
	{
		$hash = $this->__generateHash($string);

		$params = array(
			'plainText' => (string) $string,
			'encryptionKey' => $this->__encryptionKey
		);

		$return_data = $this->client->GenerateHash($params);

		return $this->__processResponse($return_data);
	}

	function testHash()
	{
		user_error('MD5 hash signing test');

		$string = sha1(rand(0,10000));
		user_error('Random string: '.$string);

		$hash = $this->__generateHash($string);
		$this->GenerateHash($string);
		$remotehash = $this->getMD5Hash();

		user_error('Local MD5: '.$hash);
		user_error('Remote MD5: '.$remotehash);

		if ( $remotehash == $hash )
		{
			user_error('MD5 hash signing works properly');
			return true;
		}
		else
		{
			user_error('MD5 hash signing doesn\'t work properly');
			return false;
		}
	}

	function getMD5Hash()
	{
		return $this->MD5Hash;
	}
	function getTransactionRef()
	{
		return $this->transactionRef;
	}
	function getTransactionNumber()
	{
		return $this->transactionNumber;
	}
	function setTransactionRef($transactionRef)
	{
		$this->transactionRef = $transactionRef;
	}
	function setTransactionNumber($transactionNumber)
	{
		$this->transactionNumber = $transactionNumber;
	}
	function setMD5Hash($MD5Hash)
	{
		$this->MD5Hash = $MD5Hash;
	}
}
?>