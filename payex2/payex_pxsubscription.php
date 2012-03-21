<?php
/*

payex_pxsubscription.php - Class for using the PxSubscription Payex webservice.
Author: Jon Helge Stensrud, Keyteq AS
Revision: 5.3

Please report bugs to support.solutions@payex.no

*/

require_once('payex.php');

class PayexPxSubscription extends Payex
{
	var $subscriptionStatus;
	var $stopDate;

	function PayexPxSubscription($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxSubscriptionWSDL());
	}

	function Stop($userType, $userRef, $subscriptionNumber)
	{
		$hash = $this->__generateHash($this->__accountNumber, $userType, $userRef, $subscriptionNumber);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'subscriptionNumber' => (string) $subscriptionNumber,
			'hash' => $hash
		);

		$return_data = $this->client->Stop($params);

		return $this->__processResponse($return_data);
	}

	function Check($userType, $userRef, $subscriptionNumber)
	{
		$hash = $this->__generateHash($this->__accountNumber, $userType, $userRef, $subscriptionNumber);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'userType' => (int) $userType,
			'userRef' => (string) $userRef,
			'subscriptionNumber' => (string) $subscriptionNumber,
			'hash' => $hash
		);

		$return_data = $this->client->Check($params);

		return $this->__processResponse($return_data);
	}

	function getStopDate()
	{
		return $this->stopDate;
	}
	function getSubscriptionStatus()
	{
		return $this->subscriptionStatus;
	}

	function setStopDate($stopDate)
	{
		$this->stopDate = $stopDate;
	}
	function setSubscriptionStatus($subscriptionStatus)
	{
		$this->subscriptionStatus = (int) $subscriptionStatus;
	}
}
?>