<?php
/* SMS purchase activation form
 *
 * TODO possible to activate expired memberships?
 */
set_include_path("../includes/");

require_once("../inside/credentials.php");
require_once("../inside/functions.php");
require_once("../inside/migration/ldap_api_functions.php");
require_once("DB.php");

require_once("../snapporder/lib/functions.php");
require_once("../snapporder/config.php");


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

// empty default form values
$validation_errors = "";
$firstname = "";
$lastname = "";
$email = "";
$activation_code = "";
$phone = "";

if( isset($_GET['n']) ) {
    // phone
	$phone = $_GET['n'];
}
if( isset($_GET['c']) ) {
	// code
	$activation_code = $_GET['c'];
}


if( isset($_POST['submit']) ) {
    $data = NULL;
    try {
        $data = validate_sms_form($_POST); // can throw
    } catch(ValidationException $e) {
        $validation_errors = $e->getMessage();

        /* Refill form values*/
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
    }
    if($data !== NULL) {
        try {
            // persist
            save_sms_form($data);

			$user = get_user($data['userid']);

            // send warm and fuzzy welcome email
            send_registration_email($data); // same as on new snapporder membership

			$url = $data['registration_url'];

            // redirect to confirmation page
            redirect("/snapporder/$url");
            die();
        } catch(InsideDatabaseException $e) {
            echo $e->getMessage();
            die();
        }
    }
}

?>

<?php include("../snapporder/header.php"); ?>
<div class="container">
    <h1 class="title">Registrer medlemskap kjøpt på SMS</h1>
    <em class="subtitle">på Det Norske Studentersamfund</em>
    <header class="about">
        <p>Hei, for at medlemskapet du har kjøpt på SMS skal være gyldig, så <strong>må</strong> du registerere deg med navn og epost.</p>
        <p class="imperative">Fullfør registreringen din ved å angi <em>telefonnummer, aktiveringskode, fornanavn</em> og <em>epost</em>.</p>
    </header>
    <section class="register-sms">
        <?php if(strlen($validation_errors) > 0 ) {
            echo "<span class=\"error\">$validation_errors</span>";
        } else {
            echo "<span class=\"error hidden\"></span>";
        } ?>
		<form method="post">
		<div class="activation-form">
            <!-- Profile -->
            <div class="form-row">
                <label for="id_phone">Telefonnummer:</label><input id="id_phone" type="text" name="phone" placeholder="Telefonnummer" value="<?php echo $phone; ?>"/>
            </div>
            <div class="form-row">
                <label for="id_activation_code">Aktiveringskode:</label><input id="id_activation_code" type="text" name="activation_code" placeholder="Aktiveringskode" value="<?php echo $activation_code; ?>"/>
            </div>
            <div class="form-row">
                <label for="id_firstname">Fornavn:</label><input id="id_firstname" type="text" name="firstname" placeholder="Fornavn" value="<?php echo $firstname; ?>"/>
            </div>
            <div class="form-row">
                <label for="id_lastname">Etternavn:</label><input id="id_lastname" type="text" name="lastname" placeholder="Etternavn" value="<?php echo $lastname; ?>"/>
            </div>
            <div class="form-row">
                <label for="id_email">Epost:</label><input id="id_email" type="email" name="email" placeholder="Passord" value="<?php echo $email; ?>"/>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">Registrer medlemskapet mitt</button>
        </form>
    </section>

<?php include("../snapporder/footer.php"); ?>
