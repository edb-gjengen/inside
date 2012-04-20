<?php

error_reporting(0);
define('PAYEX_CLIENTTYPE_NOTHING', 0);
define('PAYEX_CLIENTTYPE_ACCOUNT', 1);
define('PAYEX_CLIENTTYPE_EMAIL', 2);
define('PAYEX_CLIENTTYPE_GSM', 3);
define('PAYEX_CLIENTTYPE_EXTERNALREF', 4);

define('PAYEX_CLIENTINFO_CLIENTACCOUNT', 1);
define('PAYEX_CLIENTINFO_EMAIL', 2);
define('PAYEX_CLIENTINFO_GSM', 4);
define('PAYEX_CLIENTINFO_USERNAME', 8);
define('PAYEX_CLIENTINFO_ADDRESS', 16);
define('PAYEX_CLIENTINFO_BALANCE_CLIENT', 32);
define('PAYEX_CLIENTINFO_BALANCE_MERCHANT', 64);

define('PAYEX_USERTYPE_NOTHING', 0);
define('PAYEX_USERTYPE_ACCOUNT', 1);
define('PAYEX_USERTYPE_EMAIL', 2);
define('PAYEX_USERTYPE_GSM', 3);
define('PAYEX_USERTYPE_EXTERNALREF', 4);

define('PAYEX_EXPIREDATETYPE_DISABLED', 0);
define('PAYEX_EXPIREDATETYPE_PERIODS', 1);
define('PAYEX_EXPIREDATETYPE_DATE', 2);
define('PAYEX_EXPIREDATETYPE_SECONDS', 3);

define('PAYEX_TRANSACTIONSTATUS_SALE', 0);
define('PAYEX_TRANSACTIONSTATUS_INITIALIZE', 1);
define('PAYEX_TRANSACTIONSTATUS_CREDIT', 2);
define('PAYEX_TRANSACTIONSTATUS_AUTHORIZE', 3);
define('PAYEX_TRANSACTIONSTATUS_CANCEL', 4);
define('PAYEX_TRANSACTIONSTATUS_FAILURE', 5);
define('PAYEX_TRANSACTIONSTATUS_CAPTURE', 6);

define('PAYEX_SUBSCRIPTIONSTATUS_ACTIVE_RENEWABLE', 0);
define('PAYEX_SUBSCRIPTIONSTATUS_ACTIVE_NOTRENEWABLE', 1);
define('PAYEX_SUBSCRIPTIONSTATUS_NOTACTIVE_RENEWABLE', 2);
define('PAYEX_SUBSCRIPTIONSTATUS_STOPPED', 3);
define('PAYEX_SUBSCRIPTIONSTATUS_CANCELLED', 4);
define('PAYEX_SUBSCRIPTIONSTATUS_NOTACTIVE_NOTRENEWABLE', 5);
define('PAYEX_SUBSCRIPTIONSTATUS_NOTACTIVE_STOPPED', 6);
define('PAYEX_SUBSCRIPTIONSTATUS_NOTACTIVE_EXPIRED', 7);

define('PAYEX_CREATEDSTATUS_ERROR', 0);
define('PAYEX_CREATEDSTATUS_ALREADYEXISTS', 1);
define('PAYEX_CREATEDSTATUS_SUCCESS', 2);

define('PAYEX_ORDERSTATUS_COMPLETED', 0);
define('PAYEX_ORDERSTATUS_PROCESSING', 1);
define('PAYEX_ORDERSTATUS_NOTFOUND', 2);

//From setupexample
define("PAYEX_PXPARTNER_WSDL", 'https://external.payex.com/pxorder/pxpartner.asmx?WSDL'); 
define('PAYEX_PXPRINT_WSDL', 'https://external.payex.com/pxorder/pxprint.asmx?WSDL'); 
define('PAYEX_PXDEPOSIT_WSDL', 'https://external.payex.com/pxorder/pxdeposit.asmx?WSDL');
define('PAYEX_PXACCOUNT_WSDL', 'https://external.payex.com/pxorder/pxaccount.asmx?WSDL');
define('PAYEX_PXORDER_WSDL', 'https://external.payex.com/pxorder/pxorder.asmx?WSDL');
define('PAYEX_PXCLIENT_WSDL', 'https://external.payex.com/pxclient/pxclient.asmx?WSDL'); 
define('PAYEX_PXMERCHANT_WSDL', 'https://external.payex.com/pxmerchant/pxmerchant.asmx?WSDL');
define('PAYEX_PXSUBSCRIPTION_WSDL', 'https://external.payex.com/pxsubscription/pxsubscription.asmx?WSDL');
define('PAYEX_PXAGREEMENT_WSDL', 'https://external.payex.com/pxagreement/pxagreement.asmx?WSDL'); 

define('PAYEX_PXORDER_URL', 'https://account.payex.com/pxorder.asp'); // url to redirect user to
define('PAYEX_ACCOUNTNUMBER', 0); // payex merchant account number
define('PAYEX_ENCRYPTIONKEY', ''); // payex encryption key
?>
