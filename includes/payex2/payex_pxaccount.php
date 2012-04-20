<?php

require_once('payex.php');

/**
 * Class for using the Client/PxAccount Payex webservice.
 * Please report bugs to support.solutions@payex.no
 *
 * @author Jon Helge Stensrud, Keyteq AS
 * @version 5.3
 *
 */

class PayexPxAccount extends Payex
{
	var $transactions;

	function PayexPxAccount($setup = false)
	{
		$this->__setup($setup);
		$this->__createClient($this->__setup->getPxAccountWSDL());
	}

	function getTransactions()
	{
		return $this->transactions;
	}

	function ListTransactions($clientType, $clientRef, $maxRowCount, $filterByTransactionNumber)
	{
		$hash = $this->__generateHash($this->__accountNumber, $clientType, $clientRef, $maxRowCount, $filterByTransactionNumber);

		$params = array(
			'accountNumber' => $this->__accountNumber,
			'clientType' => $clientType,
			'clientRef' => $clientRef,
			'maxRowCount' => $maxRowCount,
			'filterByTransactionNumber' => $filterByTransactionNumber,
			'hash' => $hash
		);

		$return_data = $this->client->ListTransactions($params);

		$this->transactions = array();

		if ( $this->__processResponse($return_data) )
		{
			if ( is_array($return_data) )
				$resptext = array_pop($return_data);
			else
				$resptext = $return_data;

			// parse transactions xml.
			$xmlroot = &new KQXmlobj();
			$xmlroot->from_xml($resptext);

			// get <transactions>-tag
			if ( $transactionsXml = &$xmlroot->get_child('transactions') )
			{
				// loop through all <transaction>-tags
				while ( $transactionXml = &$transactionsXml->get_child('transaction') )
				{
					// fill Transaction transfer-object with values from xml
					$transaction = new PayexPxAccountTransaction();
					$transaction->setTransactionNumber($transactionXml->get_value('transactionNumber'));
					$transaction->setTransactionDate($transactionXml->get_value('transactionDate'));
					$transaction->setName($transactionXml->get_value('name'));
					$transaction->setTransactionDescription($transactionXml->get_value('transactionDescription'));
					$transaction->setAmount($transactionXml->get_value('amount'));
					$transaction->setCurrency($transactionXml->get_value('currency'));
					$transaction->setPaymentMethod($transactionXml->get_value('paymentMethod'));

					// add transaction to array, to be returned by $this->getTransactions()
					$this->transactions[] = $transaction;
				}
			}

			return true;
		}
		return false;
	}
}

class PayexPxAccountTransaction
{
	var $transactionNumber;
	var $transactionDate;
	var $name;
	var $transactionDescription;
	var $amount;
	var $currency;
	var $paymentMethod;

	function getTransactionNumber()
	{
		return $this->transactionNumber;
	}
	function setTransactionNumber($transactionNumber)
	{
		$this->transactionNumber = $transactionNumber;
	}
	function getTransactionDate()
	{
		return $this->transactionDate;
	}
	function setTransactionDate($transactionDate)
	{
		$this->transactionDate = $transactionDate;
	}
	function getName()
	{
		return $this->name;
	}
	function setName($name)
	{
		$this->name = $name;
	}
	function getTransactionDescription()
	{
		return $this->transactionDescription;
	}
	function setTransactionDescription($transactionDescription)
	{
		$this->transactionDescription = $transactionDescription;
	}
	function getAmount()
	{
		return $this->amount;
	}
	function setAmount($amount)
	{
		$this->amount = $amount;
	}
	function getCurrency()
	{
		return $this->currency;
	}
	function setCurrency($currency)
	{
		$this->currency = $currency;
	}
	function getPaymentMethod()
	{
		return $this->paymentMethod;
	}
	function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}
}
?>