<?php
function generate_username($data) {
    /* Clean non ascii chars */
    $firstname = preg_replace('/[^\w]/', '', $data['firstname']);
    $firstname = substr(strtolower($firstname), 0, 6);

    $lastname = preg_replace('/[^\w]/', '', $data['lastname']);
    $lastname = substr(strtolower($lastname), 0, 3);
    return $firstname.$lastname.uniqid();
}
var_dump(generate_username(array('firstname' => "Nikolai R", "lastname" => "Kristiansen")));
