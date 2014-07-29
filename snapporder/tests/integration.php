<?php
require_once("../lib/CryptoHelper.php");
require_once("../lib/functions.php");
require_once("../config.php");

define("API_URL", 'http://inside.dev');

/* Query */
function test_get_query() {
    $number = "+4745115787";
    $ch = curl_init(API_URL.'/snapporder/api/query.php?phone='.urlencode($number));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);
    $result = $crypt->decrypt(trim($result));
    $decoded_result = (array) json_decode($result);

    if($decoded_result['phone'] ===  $number) {
        echo "OK";
    } else {
        echo "FAIL";
    }
    curl_close ($ch);
}
/* Register */
function test_post_register() {
    $crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);
    $data = file_get_contents("./testuser.json");

    $data = $crypt->encrypt($data);
    $ch = curl_init(API_URL.'/snapporder/api/register.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING ,"");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));

    $result = curl_exec($ch);
    $result = $crypt->decrypt(trim($result));
    $decoded_result = (array) json_decode($result);

    if(isset($decoded_result['birthdate']) && $decoded_result['birthdate'] === "1983-01-01") {
        echo "OK";
        var_dump($decoded_result);
    } else {
        echo "FAIL\n";
        var_dump($decoded_result);
    }
    /* Register */
    curl_close ($ch);
}
function test_post_register_renewal() {
    $crypt = new CryptoHelper(SNAP_IV, SNAP_KEY);
    $data = file_get_contents("./testuser_renewal.json");

    $data = $crypt->encrypt($data);
    $ch = curl_init(API_URL.'/snapporder/api/register.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING ,"");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));

    $result = curl_exec($ch);
    $result = $crypt->decrypt(trim($result));
    $decoded_result = (array) json_decode($result);

    if(isset($decoded_result['expires']) && $decoded_result['expires'] === "2015-05-12") {
        echo "OK";
        var_dump($decoded_result);
    } else {
        echo "FAIL\n";
        var_dump($decoded_result);
    }
    /* Register */
    curl_close ($ch);
}
//test_get_query();
//test_post_register();
test_post_register_renewal();
