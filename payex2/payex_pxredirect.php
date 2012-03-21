<?php
require_once('payex.php');

/**
 * Class for using the PayexPxRedirect Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */
class PayexPxRedirect extends Payex
{
	var $redirectUrl;

	function PayexPxRedirect($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxRecurringWSDL());
	}

	function AddRedirectData($page, $parameters, $returnUrl)
	{
		$hash = $this->__generateHash($this->__accountNumber, $page, $parameters, $returnUrl);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'page' => $this->page,
			'parameters' => $parameters,
			'returnUrl' => $returnUrl,
			'hash' => $hash
		);

		$return_data = $this->client->AddRedirectData($params);
		return $this->__processResponse($return_data);
	}

	function GetRedirectData($redirectId)
	{
		$hash = $this->__generateHash($this->__accountNumber, $redirectId);
		
		$params = array(
			'accountNumber' => $this->__accountNumber,
			'redirectDataRef' => $redirectId,
			'hash' => $hash
		);
		
		$return_data = $this->client->GetRedirectData($params);
		return $this->__processResponse($return_data);
	}
	
	function setRedirectUrl($redirectUrl)
	{
		$this->redirectUrl = $redirectUrl;
	}
}
?>