<?php

require_once('payex.php');

/**
 * Class for using the PxPrint Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxPrint extends Payex
{
	function PayexPxPrint($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxPrintWSDL());
	}

	function PrintToCustomer($template, $distributionType, $firstName, $lastName, $companyName, $organizationNumber, $address1, $address2, $postNumber, $city, $country, $email, $gsm, $extraFields)
	{
		$hash = $this->__generateHash($this->__accountNumber, $template, $distributionType, $firstName, $lastName, $companyName, $organizationNumber, $address1, $address2, $postNumber, $city, $country, $email, $gsm, $extraFields);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'template' => $template,
			'distributionType' => $distributionType,
			'firstName' => $firstName,
			'lastName' => $lastName,
			'companyName' => $companyName,
			'organizationNumber' => $organizationNumber,
			'address1' => $address1,
			'address2' => $address2,
			'postNumber' => $postNumber,
			'city' => $city,
			'country' => $country,
			'email' => $email,
			'gsm' => $gsm,
			'extraFields' => $extraFields,
			'hash' => $hash
		);

		$return_data = $this->client->PrintToCustomer($params);

		return $this->__processResponse($return_data);
	}
}
?>