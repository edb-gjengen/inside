<?php
require_once("../lib/CryptoHelper.php");
require_once("../lib/functions.php");
require_once("../config.php");


/* Query */
function test_get_query() {
    $number = "+4745115787";
    //$data = file_get_contents("./testuser.json");
    //$data = $crypt->encrypt($data);
    $ch = curl_init('http://inside.dev/snapporder/api/query.php?phone='.urlencode($number));
    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json '));

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
    $ch = curl_init('http://inside.dev/snapporder/api/register.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json '));

    $result = curl_exec($ch);
    $result = $crypt->decrypt(trim($result));
    $decoded_result = json_decode($result);

    if(!isset($decoded_result->error)) {
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
test_post_register();
