<?php
require_once("inside_functions.php");

$user = new User(getCurrentUser());

$current_username = getCurrentUserName();
?>

<form id="form_migrate" target="" method="post">
    <div>
        <label for="id_username">Brukernavn</label>*<br />
        <input id="id_username" type="text" name="username" value="<?php echo strtolower($current_username);?>"><br />
        <label for="id_password">Passord</label>*<br />
        <input id="id_password" type="password" name="password" value=""><br />
        <label for="id_password_check">Passord (bekreft)</label>*<br />
        <input id="id_password_check" type="password" name="password_check" value=""><br />
    </div>
</form>
