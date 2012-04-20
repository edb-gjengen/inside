<?php
// require_once('kq_xmlobj.php');
require_once('lib/kqxmlobj.php');

// require_once(DOC_ROOT.'/include/lib/nusoap/nusoap.php');
require_once('lib/nusoap.php');

require_once('payex_defines.php');
require_once('payex_setup.php');

/**
 * Base class for using Payex webservices.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 4.1
 *
 */
class Payex
{
	var $client; // soap client
	var $__accountNumber;
	var $__setup;
	var $code;
	var $description;
	var $encryptionKey;

	/**
	 * Set configuration from given object or defines
	 *
	 * @param PayexSetup $setup
	 * @return void
	 */
	function __setup($setup)
	{
		if ( $setup === false || !is_object($setup) || get_class($setup) != 'payexsetup' )
		{
			$setup = &new PayexSetup();
			$setup->setAccountNumber(PAYEX_ACCOUNTNUMBER);
			$setup->setEncryptionKey(PAYEX_ENCRYPTIONKEY);
			$setup->setPxAgreementWSDL(PAYEX_PXAGREEMENT_WSDL);
			$setup->setPxOrderWSDL(PAYEX_PXORDER_WSDL);
			$setup->setPxMerchantWSDL(PAYEX_PXMERCHANT_WSDL);
			$setup->setPxPartnerWSDL(PAYEX_PXPARTNER_WSDL);
			$setup->setPxClientWSDL(PAYEX_PXCLIENT_WSDL);
			$setup->setPxSubscriptionWSDL(PAYEX_PXSUBSCRIPTION_WSDL);
			$setup->setPxPrintWSDL(PAYEX_PXPRINT_WSDL);
			$setup->setPxDepositWSDL(PAYEX_PXDEPOSIT_WSDL);
			$setup->setPxAccountWSDL(PAYEX_PXACCOUNT_WSDL);
			$setup->setPxOrderURL(PAYEX_PXORDER_URL);
			$setup->setPxRecurringWSDL(PAYEX_PXRECURRING_WSDL);
			$setup->setPxRedirectWSDL(PAYEX_PXREDIRECT_WSDL);
			$setup->setPxSMSWSDL(PAYEX_PXSMS_WSDL);
		}

		$this->__setup = &$setup;

		$this->code = '';
		$this->description = '';

		$this->__accountNumber = (int) $setup->getAccountNumber();
		$this->encryptionKey = $setup->getEncryptionKey();
	}


	/**
	 * Create a soap proxy for communicating with Payex
	 *
	 * @param string $wsdlUrl
	 * @return bool
	 */
	function __createClient($wsdlUrl)
	{
		$wsdl = &$this->__getSoapClient($wsdlUrl);

		if ( ($err = $wsdl->getError()) !== false )
		{
			user_error('NuSOAP: '.$err);
			return false;
		}
		else
		{
			$this->client = $wsdl->getProxy();
			return true;
		}
	}

	/**
	 * Get SoapClient from filecache, make if not found
	 *
	 * @param string $wsdlUrl
	 * @return soapclient
	 */
	function &__getSoapClient($wsdlUrl)
	{
		$wsdl = null;

		// check if caching is correctly set up
		if ( defined('PAYEX_SERIALIZEDWSDL_ROOT') )
		{
			$fileName = PAYEX_SERIALIZEDWSDL_ROOT.'/'.md5($wsdlUrl).'.wsdl.ser';

			$fh = @fopen($fileName,'r');

			if ( $fh !== false )
			{
				$clwsdl = unserialize(fread($fh,filesize($fileName)));
				$wsdl = &new nusoapclient( $clwsdl, true);
				fclose($fh);
			}
			else
			{
				$fh = fopen($fileName,'w');

				$wsdl = &new nusoapclient( $wsdlUrl, true);

				if ( $fh !== false )
				{
					fwrite($fh, serialize($wsdl->wsdl));
					fclose($fh);
				}
			}
		}
		else
		{
			$wsdl = &new nusoapclient( $wsdlUrl, true);
		}

		return $wsdl;
	}

	function getCode()
	{
		return $this->code;
	}

	function getDescription()
	{
		return $this->description;
	}

	/**
	 * Takes an infinite number of parameters. Returns the generated MD5 hash
	 *
	 * @param string
	 * @return string
	 */
	function __generateHash()
	{
		$string = '';

		$arg = func_get_args();

		for ($i=0, $c=count($arg); $i<$c; $i++)
			$string .= (string) $arg[$i];

		$string .= $this->encryptionKey;
		return strtoupper(md5($string));
	}

	/**
	 * Process the response from Payex.
	 *
	 * @param array $return_data
	 * @return bool
	 */
	function __processResponse($return_data)
	{
		$this->code = '';
		$this->description = '';

		if ( $return_data === false )
		{
			$this->code = $this->client->getError();
			$this->description = '';
			user_error('NuSOAP error. '.$this->code);
			return false;
		}
		elseif ( is_array($return_data) )
		{
			if ( isset($return_data['faultcode']) )
			{
				user_error('NuSOAP error. Faultcode: \''.$return_data['faultcode'].'\' Faultstring: \''.$return_data['faultstring'].'\' Detail: \''.$return_data['detail'].'\'');

				$this->code = $return_data['faultcode'];
				$this->description = $return_data['faultstring'];
				return false;
			}
			else
			{
				$resptext = array_pop($return_data);
			}
		}
		else
		{
			$resptext = $return_data;
		}

		// Returned XML: <payex> [...] <status><code>code</code><description>description</description></status> [...] </payex>

		// Parse XML into object-structure
		$xmlroot = &new KQXmlobj();
		$xmlroot->from_xml($resptext);

		// Check status
		if ( $status = &$xmlroot->get_child('status') ) // get status-tag
		{
			$code = $this->code = $status->get_value('code'); // get value in code-tag from status-tag
			$description = $this->description = $status->get_value('description'); // get value in description-tag from status-tag

				// loop all tags in the returned xml
				while ( $tag =& $xmlroot->get_child() )
				{
					// get the tag name
					$name = $tag->get_name();
					$subs = false;

					while ( $subtag =& $tag->get_child() )
					{
						$subs = true;
						$subname = $subtag->get_name();

						if ( method_exists($this, 'set'.ucfirst($name)) )
						{
							// run setfunction
							call_user_func(array(&$this, 'set'.ucfirst($name)), $subname, $subtag->get_value());
						}
					}

					if ( $subs === false )
					{
						// check if class has a setfunction for this var
						if ( method_exists($this, 'set'.ucfirst($name)))
						{
							// run setfunction with value from xml
							call_user_func(array(&$this, 'set'.ucfirst($name)), $tag->get_value());
						}
					}
				}

				return true;
		}
		else
		{
			user_error('Missing status-tag in payex-response');
			return false;
		}
	}
}
?>