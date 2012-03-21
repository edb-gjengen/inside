<?php
require_once('payex.php');

/**
 * Class for using the PxClient Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxClient extends Payex
{
	var $clientAccount;
	var $clientInfo;
	var $balanceInfo;
	var $creditcardRef;
	var $createdStatus;
	var $accountNumber;
	var $verificationCode;

	function PayexPxClient($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxClientWSDL());

		$this->clientInfo = array();
		$this->balanceInfo = array();
		$this->creditcardRef = '';
		$this->clientAccount = '';
		$this->createdStatus = '';
		$this->accountNumber = '';
		$this->verificationCode = '';
	}

	function ClientToClientTranfer($senderAccountNumber, $userType, $userRef, $amount, $description)
	{
		$hash = $this->__generateHash($this->__accountNumber, $senderAccountNumber, $userType, $userRef, $amount, $description);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'senderAccountNumber' => $senderAccountNumber,
			'userType' => $userType,
			'userRef' => $userRef,
			'amount' => $amount,
			'description' => $description,
			'hash' => $hash
		);

		$return_data = $this->client->ClientToClientTranfer($params);
		return $this->__processResponse($return_data);
	}

	function CreateClient($verified, $email, $gsm, $socialSecurityNumber, $password, $firstName, $lastName, $address1, $address2, $address3, $postNumber, $city, $country, $currency, $language, $externalId, $ip = '_current', $generatePxAccount = true)
	{
		if ( $ip == '_current' )
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$hash = $this->__generateHash($this->__accountNumber, (int)$verified, $email, $gsm, $socialSecurityNumber, $password, $firstName, $lastName, $address1, $address2, $address3, $postNumber, $city, $country, $currency, $language, $externalId, $ip);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'verified' => (bool)$verified,
			'email' => $email,
			'gsm' => $gsm,
			'socialSecurityNumber' => $socialSecurityNumber,
			'password' => $password,
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address1' => $address1,
			'address2' => $address2,
			'address3' => $address3,
			'postNumber' => $postNumber,
			'city' => $city,
			'country' => $country,
			'currency' => $currency,
			'language' => $language,
			'externalId' => $externalId,
			'ip' => $ip,
			'generatePxAccount' => (bool)$generatePxAccount,
			'hash' => $hash
		);

		$return_data = $this->client->CreateClient($params);
		return $this->__processResponse($return_data);
	}

	function CreateClient3($verified, $email, $gsm, $socialSecurityNumber, $password, $firstName, $lastName, $address1, $address2, $address3, $postNumber, $city, $country, $currency, $language, $externalId, $ip, $generatePxAccount)
	{
			$hash = $this->__generateHash($this->__accountNumber, (int)$verified, $email, $gsm, $socialSecurityNumber, $password, $firstName, $lastName, $address1, $address2, $address3, $postNumber, $city, $country, $currency, $language, $externalId, $ip, (int)$generatePxAccount);
			
			$params = array(
			'accountNumber' => $this->__accountNumber,
			'verified' => (bool)$verified,
			'email' => $email,
			'gsm' => $gsm,
			'socialSecurityNumber' => $socialSecurityNumber,
			'password' => $password,
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address1' => $address1,
			'address2' => $address2,
			'address3' => $address3,
			'postNumber' => $postNumber,
			'city' => $city,
			'country' => $country,
			'currency' => $currency,
			'language' => $language,
			'externalId' => $externalId,
			'ip' => $ip,
			'generatePxAccount' => (bool)$generatePxAccount,
			'hash' => $hash
		);
		
		$return_data = $this->client->CreateClient3($params);
		return $this->__processResponse($return_data);
	}
	
	function SetSocialSecurityNumberVerified($clientType, $clientRef, $socialSecurityNumber, $firstName = false, $lastName, $address1, $address2, $address3, $postNumber, $city, $country)
	{
		// If any PxClient-request should ever return a <socialSecurityNumberVerified /> tag in the response,
		// this test will avoid the invokation of the SetSocialSecurityNumberVerified-method.
		// (Returnvalues are fetched by searching for set-functions)
		if ( $firstName === false )
			return;

		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $socialSecurityNumber, $firstName, $lastName, $address1, $address2, $address3, $postNumber, $city, $country);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => (int) $clientType,
			'clientRef' => $clientRef,
			'socialSecurityNumber' => $socialSecurityNumber,
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address1' => $address1,
			'address2' => $address2,
			'address3' => $address3,
			'postNumber' => $postNumber,
			'city' => $city,
			'country' => $country,
			'hash' => $hash
		);

		$return_data = $this->client->SetSocialSecurityNumberVerified($params);
		return $this->__processResponse($return_data);
	}

	function AddCreditCard($clientType, $clientRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress)
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $cardNumber, $cardNumberExpireMonth, $cardNumberExpireYear, $cardNumberCVC, $cardHolderName, $clientIPAddress);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => (int) $clientType,
			'clientRef' => $clientRef,
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

	function AddGSMNumber($clientType, $clientRef, $gsmNumber)
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $gsmNumber);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => $clientType,
			'clientRef' => $clientRef,
			'gsmNumber' => $gsmNumber,
			'hash' => $hash
		);

		$return_data = $this->client->AddGSMNumber($params);
		return $this->__processResponse($return_data);
	}

	function SendGsmVerificationCode($verificationCode, $mobileNumber)
	{
		$hash = $this->__generateHash($this->__accountNumber, $verificationCode, $mobileNumber);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'verificationCode' => $verificationCode,
			'mobileNumber' => $mobileNumber,
			'hash' => $hash
		);

		$return_data = $this->client->SendGsmVerificationCode($params);
		return $this->__processResponse($return_data);
	}

	function AddExternalIdLink($clientType, $clientRef, $password, $externalId)
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $password, $externalId);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => (int) $clientType,
			'clientRef' => (string) $clientRef,
			'password' => (string) $password,
			'externalId' => (string) $externalId,
			'hash' => $hash
		);

		$return_data = $this->client->AddExternalIdLink($params);

		return $this->__processResponse($return_data);
	}

	function UpdateExternalUserID($externalUserIdOld, $externalUserIdNew)
	{
		user_error('Warning: PxClient.UpdateExternalUserID is obsolete. Use UpdateExternalIdLink instead.');

		$hash = $this->__generateHash($this->__accountNumber, $externalUserIdOld, $externalUserIdNew);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'externalUserIdOld' => (string) $externalUserIdOld,
			'externalUserIdNew' => (string) $externalUserIdNew,
			'hash' => $hash
		);

		$return_data = $this->client->UpdateExternalUserID($params);

		return $this->__processResponse($return_data);
	}

	function UpdateExternalIdLink($externalIdOld, $externalIdNew)
	{
		$hash = $this->__generateHash($this->__accountNumber, $externalIdOld, $externalIdNew);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'externalIdOld' => (string) $externalIdOld,
			'externalIdNew' => (string) $externalIdNew,
			'hash' => $hash
		);

		$return_data = $this->client->UpdateExternalIdLink($params);

		return $this->__processResponse($return_data);
	}

	function GetClientInfo($clientType, $clientRef, $clientInfo, $validate = false, $password = '')
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $password, (int) $validate, $clientInfo);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => (int) $clientType,
			'clientRef' => (string) $clientRef,
			'password' => (string) $password,
			'validate' => (bool) $validate,
			'clientInfo' => (int) $clientInfo,
			'hash' => $hash
		);

		$return_data = $this->client->GetClientInfo($params);

		return $this->__processResponse($return_data);
	}
	
	function GetClientInfo3($clientType, $clientRef, $password, $validate)
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $password, (int) $validate);
		
		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => (int) $clientType,
			'clientRef' => (string) $clientRef,
			'password' => (string) $password,
			'validate' => (bool) $validate,
			'hash' => $hash
		);
		
		$return_data = $this->client->GetClientInfo3($params);
		return $this->__processResponse($return_data);
	}

	function Ping()
	{
		return $this->client->Ping( array() );
	}
	function getVerificationCode()
	{
		return $this->verificationCode;
	}
	function setVerificationCode($verificationCode)
	{
		$this->verificationCode = $verificationCode;
	}
	function getClientAccount()
	{
		return $this->clientAccount;
	}
	function getUserName()
	{
		return $this->clientInfo['userName'];
	}
	function getAddress1()
	{
		return $this->clientInfo['address1'];
	}
	function getAddress2()
	{
		return $this->clientInfo['address2'];
	}
	function getAddress3()
	{
		return $this->clientInfo['address3'];
	}
	function getPostNumber()
	{
		return $this->clientInfo['postNumber'];
	}
	function getArea()
	{
		return $this->clientInfo['area'];
	}
	function getCountry()
	{
		return $this->clientInfo['country'];
	}
	function getEmail()
	{
		return $this->clientInfo['email'];
	}
	function getMobileNumber()
	{
		return $this->clientInfo['mobileNumber'];
	}
	function getBalanceClientCurrency()
	{
		return $this->balanceInfo['balanceClientCurrency'];
	}
	function getCurrencyClient()
	{
		return $this->balanceInfo['currencyClient'];
	}
	function getBalanceMerchantCurrency()
	{
		return $this->balanceInfo['balanceMerchantCurrency'];
	}
	function getCurrencyMerchant()
	{
		return $this->balanceInfo['currencyMerchant'];
	}
	function getAccountNumber()
	{
		return $this->accountNumber;
	}
	function setAccountNumber($accountNumber)
	{
		$this->accountNumber = $accountNumber;
	}
	function getCreatedStatus()
	{
		return $this->createdStatus;
	}
	function setCreatedStatus($createdStatus)
	{
		$this->createdStatus = $createdStatus;
	}
	function setClientAccount($clientAccount)
	{
		$this->clientAccount = $clientAccount;
	}
	function setClientInfo($name, $value)
	{
		$this->clientInfo[$name] = $value;
	}
	function setBalanceInfo($name, $value)
	{
		$this->balanceInfo[$name] = $value;
	}
	function setCreditcardRef($creditcardRef)
	{
		$this->creditcardRef = $creditcardRef;
	}
	function getCreditcardRef()
	{
		return $this->creditcardRef;
	}
}
?>