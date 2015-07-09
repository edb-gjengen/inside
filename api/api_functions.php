<?php
set_include_path("../includes/");
require_once("../includes/DB.php");
require_once("../inside/credentials.php");


/* Set HTTP response code */
function set_response_code($code) {
    if(!is_int($code)) {
        return false;
    }
    header('X-Ignore-This: something', true, $code);
}


function is_valid_utf8($text) {
    return mb_check_encoding($text, 'utf-8');
}


function get_db_connection($fetch_mode=NULL, $options=NULL) {
    if($options == NULL) {
        $options = array(
            'debug'       => 2,
            'portability' => DB_PORTABILITY_ALL,
        );
    }

    $conn = DB::connect(getDSN(), $options);

    if(DB :: isError($conn)) {
        echo $conn->toString();
    } else {
        if($fetch_mode != NULL) {
            $conn->setFetchMode($fetch_mode);
        }
    }

    return $conn;
}


function clean_phonenumber($pn) {
    $pn = preg_replace('/[^0-9\+]/', '', $pn); // remove everything except valid chars
    $pn = preg_replace('/^00/','+', $pn); // replace starting 00 with +
    // norwegian phone numbers
    if( strlen($pn) === 8 && ($pn[0] === "4" || $pn[0] === "9") ) {
        $pn = "+47".$pn;
    }
    // ...without +
    if( strlen($pn) === 10 && $pn[0] === "4" && $pn[1] === "7" && ($pn[2] === "4" || $pn[2] === "9") ) {
        $pn = "+".$pn;
    }
    return $pn;
}


// E.164
function valid_phonenumber($phone) {
    return preg_match('/^\+?\d{8,15}$/i', $phone);
}