<?php
require_once("api_functions.php");
require_once("../includes/DB.php");

define("API_URL", 'http://inside.dev');

/* cardnumber.php */
function test_post_card() {
    $data = json_encode(array(
        'user_id' => '1',
        'card_number' => '123456789'
    ));

    $ch = curl_init(API_URL.'/api/card.php?apikey='.USER_API_KEY_KASSA);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING ,"");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));

    $result = curl_exec($ch);
    var_dump($result);
    $decoded_result = json_decode($result, true);
    var_dump($decoded_result);

    /* Register */
    curl_close ($ch);
}

test_post_card();