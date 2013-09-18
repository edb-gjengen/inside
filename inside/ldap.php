<?php

function new_login($username_or_email, $password) {
    if(trim($username_or_email) == "" || trim($password) == "") {
        return false;
    }

    if( !LDAP_ENABLED ) {
        return legacy_login($username_or_email, $password);
    }

    $db = new Ldap(LDAP_SERVER);

    /* sanitize input */
    // TODO how?

    /* in ldap? */
    $user = ldap_get_user($username_or_email, $db);
    if($user === false) {
        /* ...no, try legacy */
        return legacy_login($username_or_email, $password);
    } else {
        /* yep! */
        return ldap_login($user['uid'], $password, $db);
    }
}

function legacy_login($username_or_email, $password) {
    if( is_mail($username_or_email) ) {
        $username_or_email = legacy_get_user_from_mail($username_or_email);
        if( $username_or_email === false) {
            return false;
        }
    }
    $uid = legacy_authenticate($username_or_email, $password);
    if(!$uid) {
        notify("Feil brukernavn eller passord.");
        return false;
    }
    $_SESSION['valid-user'] = $uid;
    $_SESSION['auth-source'] = 'legacy';
    return $uid;
}

function ldap_login($username, $password, $db) {
    /* is username in legacy db? */
    $legacy_uid = legacy_user_exists($username);
    if(!$legacy_uid) {
        /* the matching username does'nt exist in the legacy db */
        /* email admins (should not happen often) */
        notify("Tetris-spillet har eksplodert! EDB-ere er blitt varslet om problemet.");
        mail('kak-edb@studentersamfundet.no', '[Inside] Migration-LOL', 'User \''.$username.'\' exists in LDAP but not in Inside legacy database.');
        return false;
    }
    /* correct password? */
    $authenticated = ldap_authenticate($username, $password, $db);
    if(!$authenticated) {
        notify("Feil brukernavn eller passord.");
        return false;
    }
    /* Log the user in */
    $_SESSION['valid-user'] = $legacy_uid;
    $_SESSION['auth-source'] = 'ldap';
    return $legacy_uid;
}

/* TODO exception handling */
function ldap_get_user($username_or_email, $db) {
    if( is_mail($username_or_email) && $db->mailExists($username_or_email) ) {
        return $db->getUserWithMail($username_or_email);
    }
    if($db->userExists($username_or_email)) {
        return $db->getUser($username_or_email);
    }
    return false;
}

function ldap_authenticate($username, $password, $db) {
    return $db->testBind($username, $password);
}

function legacy_user_exists($username) {
    $conn = db_connect();

    /* get legacy_user_id */
    $sql = sprintf("SELECT id FROM din_user WHERE username=%s OR ldap_username=%s", $conn->quoteSmart($username), $conn->quoteSmart($username));
    $result = $conn->query($sql);
    if (DB :: isError($result) == true) {
        /* dberror */
        notify("Databasen sliter, prøv igjen senere. Error: ".$conn->getMessage());
        return false;
    }
    if ($row = & $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        return $row['id'];
    }
    return false;
}

function legacy_get_user_from_mail($mail) {
    $conn = db_connect();

    $sql = sprintf("SELECT username FROM din_user WHERE email = %s", $conn->quoteSmart($mail));
    $result = $conn->query($sql);
    if (DB :: isError($result) == true) {
        /* dberror */
        notify("Databasen sliter, prøv igjen senere.");
        return false;
    }
    if ($row = & $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        return $row['username'];
    }
    return false;
}

function legacy_authenticate($username, $password) {
    $conn = db_connect();
    /* query db with form data (password, username) */
    $sql = sprintf("SELECT id FROM din_user WHERE username = %s AND (password = PASSWORD(%s) or password = OLD_PASSWORD(%s))",
        $conn->quoteSmart($username),
        $conn->quoteSmart($password),
        $conn->quoteSmart($password));

    $result = $conn->query($sql);
    if (DB :: isError($result) != true) {
        /* more than 1 row means valid user */
        if ($result->numRows() > 0) {
            $row = $result->fetchRow(DB_FETCHMODE_ASSOC);
            return $row['id'];
        }
    }
    return false;
}

function is_mail($subject) {
    $subject = trim($subject);
    /* from: http://www.regular-expressions.info/email.html */
    $pattern = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
    $match = preg_match($pattern, $subject);

    return $match;
}

?>
