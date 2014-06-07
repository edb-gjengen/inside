<?php
/* User activation form (step 2).
 * SnappOrder first saves initial data in our temp user db
 *
 * TODO:
 * - validate user data
 * - load inside user object and populate it
 * - persist to db
 */
set_include_path("../includes/");

require_once("../inside/credentials.php");
require_once("../inside/functions.php");
require_once("../includes/DB.php");

require_once("lib/functions.php");
require_once("config.php");

if( !isset($_GET['userid']) || !isset($_GET['token']) ) {
    echo "Ugyldig aktiveringslenke. Ta kontakt med medlemskap@studentersamfundet.no hvis du har spørsmål.";
    set_response_code(400);
    die();
}

/* Connect to database */
$options = array( 'debug' => 2, 'portability' => DB_PORTABILITY_ALL );
$conn = DB::connect(getDSN(), $options);
if(DB :: isError($conn)) {
    echo $conn->toString();
    set_response_code(500);
    echo 'Could not connect to DB';
    die();
}
$conn->setFetchMode(DB_FETCHMODE_ASSOC);
/* Fetch user and check for valid token */
$user = get_user($_GET['userid']);
if( !check_token($user, $_GET['token'], SECRET_KEY)) {
    echo "Ugyldig aktiveringslenke. Ta kontakt med medlemskap@studentersamfundet.no hvis du har spørsmål.";
    set_response_code(400);
    die();
}

if( isset($_POST['submit']) ) {
    // TODO validate and persist
    var_dump($_POST);
}

?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title></title>
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />

    <!--<script src="js/jquery-1.11.0.js"></script>
    <script src="bower_components/foundation/js/foundation.js"></script>
    <script src="js/app.js"></script>-->

    <!-- GA? -->

    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">
    <h1 class="title">Aktiver medlemskapet ditt</h1>
    <header class="about">
        <p>Hei <strong><?php echo $user['firstname']; ?></strong>, du er veldig nære å kunne:</p>
        <ul class="incentives">
            <li>Bruke trådløsnett på Chateau Neuf</li>
            <li>Bli aktiv på Studentersamfundet</li>
        </ul>
        <h2>Din profil</h2>
        <ul class="profile">
            <?php
            $visible_vars = array("firstname", "lastname", "email", "memberid");
            foreach($user as $key=>$val) {
                if( in_array($key, $visible_vars) ) {
                    echo "<li><strong>".ucfirst($key)."</strong>: $val</li>\n";
                }
            }
            ?>
        </ul>
    </header>
    <section class="activation">
        <h2>Kontodetaljer</h2>
        <form method="post" class="activation-form">
            <label for="id_username">Brukernavn:</label><input id="id_username" type="text" name="username" placeholder="Brukernavn"/>
            <label for="id_password">Passord:</label><input id="id_password" type="password" name="password" placeholder="Passord"/>
            <label for="id_date_of_birth">Fødselsdato:</label><input id="id_date_of_birth" type="text" name="date_of_birth" placeholder="Fødselsdato"/>
            <label for="id_place_of_study">Studiested:</label><input id="id_place_of_study" type="text" name="place_of_study" placeholder="Studiested"/>
            <button type="submit" name="submit" class="btn-submit">Aktiver medlemskapet</button>
        </form>
    </section>
    <footer>
        <img src="https://brukerinfo.neuf.no/static/style/img/DNSlogo.png">
        <img src="http://www.snapporder.com/img/snapporder_logo_dark_liggende.png">
    </footer>
</div>
</body>
</html>
