<?php
class PayexSetup
{
	var $accountNumber;
	var $encryptionKey;
	var $pxAccountWSDL;
	var $pxAgreementWSDL;
	var $pxClientWSDL;
	var $pxDepositWSDL;
	var $pxMerchantWSDL;
	var $pxOrderURL;
	var $pxOrderWSDL;
	var $pxPartnerWSDL;
	var $pxPrintWSDL;
	var $pxRecurringWSDL;
	var $pxRedirectWSDL;
	var $pxSMSWSDL;
	var $pxSubscriptionWSDL;
	
	function PayexSetup()
	{
		$this->accountNumber = '';
		$this->encryptionKey = '';
		$this->pxAgreementWSDL = '';
		$this->pxOrderWSDL = '';
		$this->pxMerchantWSDL = '';
		$this->pxPartnerWSDL = '';
		$this->pxClientWSDL = '';
		$this->pxSubscriptionWSDL = '';
		$this->pxDepositWSDL = '';
		$this->pxPrintWSDL = '';
		$this->pxAccountWSDL = '';
		$this->pxSubscriptionWSDL = '';
		$this->pxRecurringWSDL = '';
		$this->pxRedirectWSDL = '';
		$this->pxSMSWSDL = '';
		$this->pxOrderURL = '';
	}

	function getPxSMSWSDL()
	{
		return $this->pxSMSWSDL;
	}
	function setPxSMSWSDL($pxSMSWSDL)
	{
		$this->pxSMSWSDL = $pxSMSWSDL;
	}
	function getPxRedirectWSDL()
	{
		return $this->pxRedirectWSDL;
	}
	function setPxRedirectWSDL($pxRedirectWSDL)
	{
		$this->pxRedirectWSDL = $pxRedirectWSDL;
	}
	function getPxRecurringWSDL()
	{
		return $this->pxRecurringWSDL;
	}
	function setPxRecurringWSDL($pxRecurringWSDL)
	{
		$this->pxRecurringWSDL = $pxRecurringWSDL;
	}
	function getPxPrintWSDL()
	{
		return $this->pxPrintWSDL;
	}
	function setPxPrintWSDL($pxPrintWSDL)
	{
		$this->pxPrintWSDL = $pxPrintWSDL;
	}
	function getPxDepositWSDL()
	{
		return $this->pxDepositWSDL;
	}
	function setPxDepositWSDL($pxDepositWSDL)
	{
		$this->pxDepositWSDL = $pxDepositWSDL;
	}
	function getPxAccountWSDL()
	{
		return $this->pxAccountWSDL;
	}
	function setPxAccountWSDL($pxAccountWSDL)
	{
		$this->pxAccountWSDL = $pxAccountWSDL;
	}
	function getAccountNumber()
	{
		return $this->accountNumber;
	}
	function setAccountNumber($accountNumber)
	{
		$this->accountNumber = $accountNumber;
	}
	function getEncryptionKey()
	{
		return $this->encryptionKey;
	}
	function setEncryptionKey($encryptionKey)
	{
		$this->encryptionKey = $encryptionKey;
	}
	function getPxAgreementWSDL()
	{
		return $this->pxAgreementWSDL;
	}
	function setPxAgreementWSDL($pxAgreementWSDL)
	{
		$this->pxAgreementWSDL = $pxAgreementWSDL;
	}
	function getPxOrderWSDL()
	{
		return $this->pxOrderWSDL;
	}
	function setPxOrderWSDL($pxOrderWSDL)
	{
		$this->pxOrderWSDL = $pxOrderWSDL;
	}
	function getPxMerchantWSDL()
	{
		return $this->pxMerchantWSDL;
	}
	function setPxMerchantWSDL($pxMerchantWSDL)
	{
		$this->pxMerchantWSDL = $pxMerchantWSDL;
	}
	function getPxPartnerWSDL()
	{
		return $this->pxPartnerWSDL;
	}
	function setPxPartnerWSDL($pxPartnerWSDL)
	{
		$this->pxPartnerWSDL = $pxPartnerWSDL;
	}
	function getPxClientWSDL()
	{
		return $this->pxClientWSDL;
	}
	function setPxClientWSDL($pxClientWSDL)
	{
		$this->pxClientWSDL = $pxClientWSDL;
	}
	function getPxSubscriptionWSDL()
	{
		return $this->pxSubscriptionWSDL;
	}
	function setPxSubscriptionWSDL($pxSubscriptionWSDL)
	{
		$this->pxSubscriptionWSDL = $pxSubscriptionWSDL;
	}
	function getPxOrderURL()
	{
		return $this->pxOrderURL;
	}
	function setPxOrderURL($pxOrderURL)
	{
		$this->pxOrderURL = $pxOrderURL;
	}
}
?>