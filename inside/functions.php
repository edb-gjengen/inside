<?php
define("GIF", "1");
define("JPG", "2");
define("PNG", "3");

$extraScriptParams = Array ();

require_once('credentials.php');

function db_connect($host = "default") {

	if (!isset ($GLOBALS['db_conn'][$host])) {
		$conn = & DB :: connect(getDSN($host), array('debug' => 2));
		if (DB :: isError($conn)) {
			if ($conn->getCode() == -24) {
				error("Databasen er for øyeblikket utilgjengelig, vennligst forsøk igjen siden.");
				return false;
			}
			else {
				error("Database error: " . $conn->toString());
				return false;
			}
		}
		else {
			$GLOBALS['db_conn'][$host] = & $conn;
		}
	}
	return $GLOBALS['db_conn'][$host];
}

function retrieveValues($obj, $values) {
	$vars = get_class_vars(get_class($obj));
	for ($i = 0; $i < count($vars); $i++) {
		eval ("\$obj->" . key($vars) . " = \$values['" . key($vars) . "'];");
		next($vars);
	}
}

// script_param() extracts an input parameter from the script execution
// environment. If extra backslashes were added due to magic_quotes_gpc being
// enabled, it strips them using the remove_backslashes() function.
// track_vars is assumed to be enabled, but nothing is assumed about
// magic_quotes_gpc, and the function does not require register_globals to
// be enabled. 

// remove_backslashes() takes into account whether the value is a scalar or
// an array.  It is recursive in case you create a form that takes advantage
// of the ability to created nested input parameters in PHP 4 and up.

//
function remove_backslashes($val) {
	if (!is_array($val)) {
		$val = stripslashes($val);
	}
	else {
		reset($val);
		while (list ($k, $v) = each($val)) {
			$val[$k] = remove_backslashes($v);
		}
	}
	return ($val);
}

function scriptParam($name) {
	if (isset ($val))
		unset ($val);
	if (isset ($GLOBALS['extraScriptParams'][$name])) {
		$val = $GLOBALS['extraScriptParams'][$name];
	}
	else
		if (isset ($_POST[$name])) {
			$val = mysql_real_escape_string($_POST[$name]);
		}
		else
			if (isset ($_GET[$name])) {
				$val = $_GET[$name];
			}
			else
				if (isset ($_COOKIE[getCurrentUser()][$name])) {
					$val = $_COOKIE[getCurrentUser()][$name];
				}
	if (isset ($val) && get_magic_quotes_gpc()) {
		$val = remove_backslashes($val);
	}
	// return @$val rather than $val to prevent "undefined value"
	// messages in case $val is unset and warnings are enabled
	return (@ $val);
}

function getCurrentUser() {
	if (isset ($_SESSION['valid-user'])) {
		return $_SESSION['valid-user'];
	}
	else {
		return 0;
	}
}

function getUseridFromCardno($cardno) {
	$conn = db_connect();
	$sql = sprintf("SELECT id FROM din_user u " . "WHERE cardno = %s ", $cardno);
	$result = & $conn->query($sql);
	if ($result->numRows() == 0) {
		return false;
	}
	else {
		$row = & $result->fetchRow(DB_FETCHMODE_ORDERED);
		return $row[0];
	}
}

function getUseridFromEmail($email) {
	$conn = db_connect();
	$sql = sprintf("SELECT id FROM din_user u " . "WHERE email = '%s' ", $email);
	$result = & $conn->query($sql);
	if ($result->numRows() == 0) {
		return false;
	}
	else {
		$row = & $result->fetchRow(DB_FETCHMODE_ORDERED);
		return $row[0];
	}
}

function getUseridFromPhone($phoneno) {
	$conn = db_connect();
	$sql = sprintf("SELECT user_id FROM din_userphonenumber WHERE number = '%s' or number = '+47%s'", $phoneno, $phoneno);
	$result = & $conn->query($sql);
	if ($result->numRows() == 0) {
		return false;
	}
	else {
		$row = & $result->fetchRow(DB_FETCHMODE_ORDERED);
		return $row[0];
	}
}

function checkForumUsername($username, $password) {
	$conn = db_connect("forum");
	$sql = sprintf("SELECT user_id FROM phpbb_users u " . "WHERE username_clean = '%s' ", strtolower($username));
    /* TODO: What happends if the forum db does not exist? How do you print the error? */
	$result = & $conn->query($sql);
	if ($result->numRows() == 0) {
		return "not-used";
	}
	else {
		$sql = sprintf("SELECT user_id FROM phpbb_users u " . "WHERE username_clean = '%s' " . "AND user_password = '%s'", strtolower($username), md5($password));
		$result = & $conn->query($sql);
		if ($result->numRows() == 0) {
			return "wrong-password";
		}
		else {
			return "auth-ok";
		}
	}
}

function isAdmin($id = NULL) {
	$conn = db_connect();

	if ($id == NULL) {
		$id = getCurrentUser();
	}

	$sql = "SELECT ugr.user_id 
		              FROM din_usergrouprelationship ugr
		              WHERE ugr.user_id = $id
		              AND ugr.group_id = 3";

	$result = & $conn->query($sql);
	if (DB :: isError($result) == true) {
		error("Brukergrupper: " . $result->toString());
	}
	else {
		if ($result->numRows() > 0) {
			return true;
		}
	}
	return false;
}

function isBoardmember($id = NULL) {
	$conn = db_connect();

	if ($id == NULL) {
		$id = getCurrentUser();
	}

	$sql = "SELECT ugr.user_id 
		              FROM din_usergrouprelationship ugr
		              WHERE ugr.user_id = $id
		              AND ugr.group_id = 61";

	$result = & $conn->query($sql);
	if (DB :: isError($result) == true) {
		error("Brukergrupper: " . $result->toString());
	}
	else {
		if ($result->numRows() > 0) {
			return true;
		}
	}
	return false;
}

function isMember($id = NULL) {
	if ($id == NULL) {
		$id = getCurrentUser();
	}
	$user = new User($id);
	return $user->isMember();
}

function isActive($id = NULL) {
	if ($id == NULL) {
		$id = getCurrentUser();
	}
	$user = new User($id);
	return $user->isActive();
}

function membershipExpired($id = NULL) {
	if ($id == NULL) {
		$id = getCurrentUser();
	}
	$user = new User($id);
	return $user->hasExpired();
}

function membershipNextYear($id = NULL) {
	if (date("Y-m-d") > date("Y-11-15")) { //Only valid if after given date
		if ($id == NULL) {
			$id = getCurrentUser();
		}
		$user = new User($id);
		return $user->expires != date("Y") . "-12-31";
	}
	else {
		return true;
	}
}

function validCardno($cardno) {
	$cardno = trim($cardno);
	$conn = db_connect();
	$sql = "SELECT id 
		              FROM din_usedcardno
		              WHERE id = $cardno";
	$result = & $conn->query($sql);
	if (DB :: isError($result) == true) {
		error("Aktiveringsnummer: " . $result->toString());
	}
	else {
		if ($result->numRows() == 0) {
			return true;
		}
	}
	return false;
}

function loggedIn() {
	return getCurrentUser();
}

function getCurrentUserName() {
	$id = getCurrentUser();
	if ($id > 0) {
		$conn = db_connect();
		$sql = "SELECT username FROM din_user WHERE id = $id";

		$result = & $conn->query($sql);
		if (DB :: isError($result) == true) {
			error("Username: " . $result->toString());
		}
		else {
			if ($result->numRows() > 0) {
				$row = & $result->fetchRow(DB_FETCHMODE_ORDERED);
				return $row[0];
			}
		}
	}
}

function getUserIP() {
	return $_SERVER['REMOTE_ADDR'];
}

function getSectionFromPage($page) {
	global $sections;
	if (isset ($sections[$page])) {
		return $sections[$page];
	}
	else {
		return "quick";
	}
}

function script_name() {
	if (isset ($_SERVER["PHP_SELF"])) {
		return ($_SERVER["PHP_SELF"]);
	}
}

/**
 * Returns path to script
 */
function scriptPath() {
	$file = str_replace('\\', '/', __FILE__);
	return substr($file, 0, strrpos($file, "/"));
}

function handle_error($errno, $errstr, $errfile, $errline, $errcontext) {
	error("$errno: $errstr");
}

function error($msg) {
    if(class_exists('Errors')) {
        Errors :: addError($msg);
    } else {
        var_dump($msg);
    }
}

function notify($msg) {
	Messages :: addMSG($msg);
}

function isDate($date) {
	return (preg_match("/(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])/i", $date));
}

//Global function for formating datetime
function formatDatetime($datetime, $type = "general") {
	$locale = Array (
		'no_NO',
		'nor_nor'
	);
	setlocale(LC_TIME, $locale);
	if ($type == "td") {
		return strftime("%d/%m-%y", strtotime($datetime));
	}
	else {
		return strftime("%A %d. %B %Y, kl.  %H:%M", strtotime($datetime));
	}
}

function formatDatetimeShort($datetime, $type = "general") {
	if ($type == "td") {
		return strftime("%d/%m-%y", strtotime($datetime));
	}
	else {
		return strftime("%d. %b. kl. %H:%M", strtotime($datetime));
	}
}

function formatDatetimeYearShort($datetime, $type = "general") {

	return strftime("%y - %d. %b kl. %H:%M", strtotime($datetime));

}

function formatDate($datetime, $type = "general") {
	if ($type == "td") {
		return strftime("%d/%m-%y", strtotime($datetime));
	}
	else {
		return strftime("%d. %B, %Y", strtotime($datetime));
	}
}

function formatTime($time) {
	return strftime("%H:%M", strtotime($time));
}

function formatPrice($price) {
	return "NOK " . $price . ",-";
}

function formatPhone($number) {
	$new = preg_replace("/(\d{2})(\d{2})(\d{2})(\d{2})/", "\\1 \\2 \\3 \\4", $number);
	return $new;
}
/*
 *Function for handling uploaded files.
 */
function new_file($file, $dir) {
	$newFile = $file['tmp_name'];
	$newFile_name = "xxx" . $file['name']; //Tempname with suffix to prevent overwriting existing files
	$newFile_size = $file['size'];
	$newFile_type = $file['type'];
	$newFile_error = $file['error'];

	if ($newFile_error > 0) {
		switch ($newFile_error) {
			case 1 :
				error("Upload_max_fileszie breached (ini)");
				break;
			case 2 :
				error("Upload max_file_size breached (html)");
				break;
			case 3 :
				error("Upload not completed");
				break;
			case 4 :
				error("No file uploaded");
				break;
		}
		return false;

	}
	else {

		$imagesize = getimagesize($newFile);
		switch ($imagesize[2]) {
			case 1 :
			case 2 :
			case 3 :
				$f = true;
				break;
			default :
				error("Invalid format. Only jpeg, png and gif are supported in this version.");
				return false;
		}

		if ($f) {
			if ($dir == 'products') {
			  $dir = 'inside/images/products/';
			}else {
			  $dir = "inside/images/uploads/$dir/";
			}
			$upFile = $dir.$newFile_name;

			if (is_uploaded_file($newFile)) {
				if ($newFile_type == "IMAGE") {
					if ($imagesize[0] > 1280 || $imagesize[1] > 1280) {
						$maxwidth = 1280;
						$maxheight = 1280;
						$quality = 100;
						picResizeImage($newFile, $maxwidth, $maxheight, $quality);
					}
				}

				if (!move_uploaded_file($newFile, $upFile)) {
					error("Error moving file!");
					return false;
				}
				else {
					return $newFile_name;
				}
			}
		}
	}
}

function rename_file($old_name, $id, $dir, $report = true) {
	if ($dir == 'products')
		$dir = 'inside/images/products/';
	else
  		$dir = "inside/images/uploads/$dir/";

	$ext = strtolower( pathinfo($old_name, PATHINFO_EXTENSION) );
	$old_name = $dir . $old_name;
	$new_name = $dir . $id . "." . $ext;
	deleteFile($new_name, false);

	if (rename($old_name, $new_name))
		return true;
	else {
		if ($report == true)
			error("Could not rename file.");
		return false;
	}
}

function deleteFile($file, $report = true) {
	if (file_exists($file)) {
		if (unlink($file)) {
			if ($report == true) {
				notify("File deleted.");
			}
			return true;
		}
		else {
			return false;
		}
	}
	else {
		if ($report == true) {
			notify("No file to delete.");
		}
		return true;
	}
}

function picResizeImage($image, $maxwidth = NULL, $maxheight = NULL, $quality = NULL) {

	list ($width, $height, $format) = getimagesize($image);

	// Define content type and load source
	switch ($format) {
		case 0 :
			break;
		case 1 :
			$source = imagecreatefromgif($image);
			break;
		case 2 :
			$source = imagecreatefromjpeg($image);
			break;
		case 3 :
			$source = imagecreatefrompng($image);
			break;
	}

	if (isset ($maxwidth) && isset ($maxheight)) {
		// Get new sizes
		if ($width < $maxwidth) {
			$newwidth = $width;
			$newheight = $height;
		}
		else {
			$div = $maxwidth / $width;
			$newwidth = $width * $div;
			$newheight = $height * $div;
		}
		if ($newheight > $maxheight) {
			$div = $maxheight / $newheight;
			$newwidth = $newwidth * $div;
			$newheight = $newheight * $div;
		}
	}
	else
		if (isset ($maxwidth)) {
			// Get new sizes
			if ($width < $maxwidth) {
				$newwidth = $width;
				$newheight = $height;
			}
			else {
				$div = $maxwidth / $width;
				$newwidth = $width * $div;
				$newheight = $height * $div;
			}
		}
		else
			if (isset ($maxheight)) {
				// Get new sizes
				if ($height < $maxheight) {
					$newwidth = $width;
					$newheight = $height;
				}
				else {
					$div = $maxheight / $height;
					$newwidth = $width * $div;
					$newheight = $height * $div;
				}
			}

	// Load new image
	if ($format != 2) {
		$newimage = imagecreate($newwidth, $newheight);
		$color = ImageColorAllocate($newimage, 255, 255, 255);
		imagefill($newimage, 0, 0, $color);
	}
	else {
		$newimage = imagecreatetruecolor($newwidth, $newheight);
	}
	// Resize image
	imagecopyresized($newimage, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	// Output image
	switch ($format) {
		case 0 :
			break;
		case 1 :
			imagegif($newimage, $image);
			break;
		case 2 :
			imagejpeg($newimage, $image, $quality);
			break;
		case 3 :
			imagepng($newimage, $image);
			break;
	}
}

function checkAuth($actionName = NULL) {

	if ($actionName != NULL) {
		if (isset ($_SESSION['valid-user'])) {
			$user_id = $_SESSION['valid-user'];
		}
		else
			if ($actionName == 'perform-log-in' || $actionName == 'perform-order-code') {
				return true;
			}
			else {
				return false;
			}

		if (isset ($_SESSION[$actionName]) && $_SESSION[$actionName] == true) {
			//return true;
		}

		$conn = db_connect();

		$sql = "SELECT ugr.user_id
				            FROM din_usergrouprelationship ugr, din_group g,
				              din_actiongrouprelationship agr, din_action a
				            WHERE ugr.group_id = g.id
				            AND g.id = agr.group_id
				            AND agr.action_id = a.id
				            AND a.name = '$actionName'
				            AND ugr.user_id = $user_id";
		$result = $conn->query($sql);
		if (DB :: isError($result) != true) {
			if ($result->numRows() > 0) {
				$_SESSION[$actionName] = true;
				return true;
			}
			else {
				$_SESSION[$actionName] = false;
			}
		}
		return false;
	}
	else {
		return true;
	}
}

function checkPassword($userid, $verificationCode) {
	  $sperret = array('37403'=>"Betaling via kort ble ikke belastet, vennligst kontakt Glassbaren på Chateau Neuf eller medlemskap@studentersamfundet.no\n\n",
    				 '100505'=>"SMS ble feilbelastet, hadde medlemskap\n\n");
    if(@$sperret[$userid])
    {
    	$msg = "Denne koden er sperret: {$sperret[$userid]}<br /><br />";
    	notify($msg);
    	return false;
    }

	if (trim($verificationCode) == substr(crypt(trim($userid), 1813), 2, 6)) {
		return true;
	}
	else {
		return false;
	}
}

function displayCode($code) {
	print "<p>";
	print "Kortnummer: $code<br />";
	print "Kode: " . getCode($code) . "<br />";
	if (isValidCode($code)) {
	  print "Koden har ikke vært brukt";
	} else {
	  print "<font color=\"red\">Koden har allerede vært brukt</font>";
	}
	print "</p>";
}

function getCode($code) {
	return substr(crypt(trim($code), 1813), 2, 6);
}

function isValidCode($code) {
	$conn = db_connect();
    $sql = "SELECT id FROM din_user WHERE cardno=$code";
    $result = $conn->query($sql);
    if (DB :: isError($result) != true) {
      if ($result->numRows() > 0) {
        return false;
      }
    } else {
      error($result->toString());
      notify("En ukjent feil oppstod. Vennligst kontakt administrator.");
    }
    return true;
}


function generatePassword() {
	$seed = rand(1, 2000);
	$password = substr(crypt($seed, 666), 2, 6);
	return $password;
}

function createTransactionId() {
	$id = getCurrentUser() . "-" . date("YmdHis");
	return $id;
}

/**
 * Function for deciding if the current user is responsible
 * for the type of object referenced in $_GET or $_POST variables.
 ***/
function checkResponsible() {
	$subject = $_REQUEST;
	ob_start();
	//var_dump($subject);
	$subject = ob_get_clean();
	if (strstr($subject, "id") != false) {
		$pattern = "/\w+id/";
		$matches = Array ();
		preg_match($pattern, $subject, $matches);
		$typeid = $matches[0];
		$id = scriptParam($typeid);
		if ($id == NULL) {
			return false;
		}
		$type = str_replace("id", "", $typeid);
	}
	else {
		return false;
	}

	$user = getCurrentUser();

	$conn = db_connect();
	if ($type == "job") {
		$sql = "SELECT id
				                    FROM din_job
				                    WHERE id = $id
				                    AND user_id_registered = $user";
		$result = $conn->query($sql);
		if (DB :: isError($result) != true) { //Checks if registered by current user
			if ($result->numRows() > 0) {
				return true;
			}
			else { //checks if job belongs to same group as user
				$sql = "SELECT j.id
								                                FROM din_group g, din_job j, 
								                                     din_usergrouprelationship ugr, din_position p, din_division d
								                                WHERE j.id = $id
								                                AND j.position_id = p.id
								                                AND p.division_id = d.id
								                                AND d.id = g.division_id
								                                AND g.id = ugr.group_id
								                                AND ugr.user_id = $user
								                                AND g.admin = 1";
				$result = $conn->query($sql);
				if (DB :: isError($result) != true) {
					if ($result->numRows() > 0) {
						return true;
					}
				}
				else {
					error("Check responsible: " . $result->toString());
				}
			}
		}
		else {
			error("Check responsible: " . $result->toString());
		}
	}
	else
		if ($type == "event") {
			$sql = "SELECT id
						                        FROM din_event
						                        WHERE id = $id
						                        AND user_id_responsible = $user";
			$result = $conn->query($sql);
			if (DB :: isError($result) != true) { //Checks if registered by current user
				if ($result->numRows() > 0) {
					return true;
				}
			}
			else {
				error("Check responsible: " . $result->toString());
			}
		}
		else
			if ($type == "concert") {
				$sql = "SELECT c.id
								                                FROM din_group g, program c, 
								                                     din_usergrouprelationship ugr, din_division d
								                                WHERE c.id = $id
								                                AND c.arr = d.id
								                                AND d.id = g.division_id
								                                AND g.id = ugr.group_id
								                                AND ugr.user_id = $user
								                                AND g.admin = 1";
				$result = $conn->query($sql);
				if (DB :: isError($result) != true) { //Checks if registered by current user
					if ($result->numRows() > 0) {
						return true;
					}
				}
				else {
					error("Check responsible: " . $result->toString());
				}
			}
			else
				if ($type == "user") {
					$sql = "SELECT DISTINCT d.username " .
					"FROM din_user d, din_user e, din_usergrouprelationship dg, din_usergrouprelationship eg, din_group dgg, din_group egg " .
					"WHERE d.id = $user " .
					"AND dg.user_id=d.id AND dg.group_id=dgg.id " .
					"AND dgg.division_id = egg.division_id AND egg.admin = 1 " .
					"AND egg.id = eg.group_id AND eg.user_id = $id";
					$result = $conn->query($sql);
					if (DB :: isError($result) != true) { //Checks if registered by current user
						if ($result->numRows() > 0) {
							return true;
						}
					}
					else {
						error("Check responsible - user: " . $result->toString());
					}
				}
				else
					if ($type == "usergrouprelationship") {
						$sql = "SELECT DISTINCT d.username " .
						"FROM din_user d, din_user e, din_usergrouprelationship dg, din_usergrouprelationship eg, din_group dgg, din_group egg " .
						"WHERE d.id = $user " .
						"AND dg.user_id=d.id AND dg.group_id=dgg.id " .
						"AND dgg.division_id = egg.division_id AND egg.admin = 1 " .
						"AND egg.id = eg.group_id AND eg.user_id = $id";
						$result = $conn->query($sql);
						if (DB :: isError($result) != true) { //Checks if registered by current user
							if ($result->numRows() > 0) {
								return true;
							}
						}
						else {
							error("Check responsible - usergrouprelationship: " . $result->toString());
						}
					}
					else
						if ($type == "division") {
							$sql = "SELECT d.id
														                                  FROM din_group g,
														                                       din_usergrouprelationship ugr, din_division d
														                                  WHERE d.id = $id
														                                  AND d.id = g.division_id
														                                  AND g.id = ugr.group_id
														                                  AND ugr.user_id = $user
														                                  AND g.admin = 1";
							$result = $conn->query($sql);
							if (DB :: isError($result) != true) {
								if ($result->numRows() > 0) {
									return true;
								}
							}
							else {
								error("Check responsible: " . $result->toString());
							}
						}
						else
							if ($type == "position") {
								$sql = "SELECT p.id
																                                      FROM din_group g, din_position p,
																                                           din_usergrouprelationship ugr, din_division d
																                                      WHERE p.id = $id
																                                      AND p.division_id = d.id
																                                      AND d.id = g.division_id
																                                      AND g.id = ugr.group_id
																                                      AND ugr.user_id = $user
																                                      AND g.admin = 1";
								$result = $conn->query($sql);
								if (DB :: isError($result) != true) {
									if ($result->numRows() > 0) {
										return true;
									}
								}
								else {
									error("Check responsible: " . $result->toString());
								}
							}

	return false;
}

function displayOptionsMenu($id, $name, $type, $actionName = NULL, $editable = true, $next_page = NULL) {
	if ($next_page == NULL) {
		$page = "display-" . $type . "s";
	}
	else {
		$page = $next_page;
	}

	if (checkAuth($actionName) || checkResponsible() || ($type == "position" && checkAuth("view-register-job"))) {
?>
    <div class="optionsMenu"><?php


		if (checkAuth($actionName) || checkResponsible()) {
?>
      <a href="javascript:
               if(confirm('<?php print getConfirmDeleteText($name); ?>')){
               location='index.php?action=delete-<?php print $type; ?>&amp;page=<?php print $page; ?>&amp;<?php print $type;?>id=<?php print $id; ?>';}"><?php print DELETE; ?> <?php print $name; ?></a>
<?php			if ($editable) {?>
      <a href="index.php?page=edit-<?php print $type; ?>&amp;<?php print $type; ?>id=<?php print $id; ?>"><?php print EDIT; ?> <?php print $name; ?></a>
      <?php


		}
	}

	if ($type == "position" && checkAuth("view-register-job")) {
?> 
      <a href="index.php?page=register-job&amp;positionid=<?php print $id; ?>">lys ut denne stillingen</a>
<?php


	}
?>
    </div>

<?php


}
}

function displayOptionsMenuTable($id, $name, $type, $actionName = NULL, $editable = true, $next_page = NULL) {
	if (checkAuth($actionName)) {
		if ($editable == true) {
			print ("<td class=\"edit\"><a title=\"edit\" class=\"icon-16x16\" href=\"index.php?page=edit-$type&amp;" . $type . "id=$id\"><img src=\"graphics/edit.png\" alt=\"edit\" $name\" /></a></td>");
		}
		if ($type == "documentcategory" || $type == "eventcategory" || $type == "jobcategory") {
			print ("<td class=\"delete\"><a title=\"delete\" href=\"javascript:
						                                   if(confirm('" . getConfirmDeleteText($name) . "')){
						                                     deleteObject('$type', $id);}\"><img src=\"graphics/delete.png\" alt=\"" . DELETE . " $name\" /></a></td>");

		}
		else {
			if (is_array($id)) {
				$keys = array_keys($id);
				$id_string = "";
				foreach ($keys as $k) {
					$id_string .= "&amp;" . $k . "=" . $id[$k];
				}
				$id_string = str_replace("_", "", $id_string);
			}
			else {
				$id_string = "&amp;" . $type . "id=" . $id;
			}
			if ($next_page == NULL) {
				$page = "display-" . $type . "s";
			}
			else {
				$page = $next_page;
			}
			print ("<td class=\"delete\"><a title=\"delete\" href=\"javascript:
						                                   if(confirm('" . getConfirmDeleteText($name) . "')){
						                                   location='index.php?action=delete-$type" . $id_string . "&amp;page=$page';}\"><img src=\"graphics/delete.png\" alt=\"" . DELETE . " $name\" /></a></td>");
		}
	}
	else {
		print ("<td></td>");
		return false;
	}
}

function getEditLink($id, $type) {
	$link = ("javascript:
		                    if(confirm('" . getConfirmDeleteText($type) . "')){
		                             editObject('$type', $id);}");
	return $link;
}

function getEditText($name) {
	//  return "<img src=\"graphics/edit.png\" alt=\"edit $name\" />";
	return "edit";
}

function getDeleteLink($id, $type) {
	$link = ("javascript:
		                    if(confirm('" . getConfirmDeleteText($type) . "')){
		                             deleteObject('$type', $id);}");
	return $link;
}

function getDeleteText($name) {
	//  return "<img src=\"graphics/delete.png\" alt=\"delete $name\" />";
	return "delete";
}

function getConfirmDeleteText($name) {
	return ("Vil du virkelig slette $name?\\n\\nDenne handlingen kan ikke angres!");
}

function displayOptionsMenuCalendar($id, $name, $type, $actionName, $month) {
	if (checkAuth($actionName)) {
		print ("<a title=\"delete\" class=\"delete\" href=\"javascript:
				                               if(confirm('" . getConfirmDeleteText($name) . "')){
				                               location='index.php?action=delete-$type&amp;" . $type . "id=$id&amp;page=display-" . $type . "s-calendar&amp;month=$month';}\"><img src=\"graphics/delete_small.png\" alt=\"" . DELETE . " $name\" /></a>");
	}
}

function prepareForHTML($text) {
	$text = stripslashes($text);
	$text = str_replace("[b]", "<strong>", $text);
	$text = str_replace("[/b]", "</strong>", $text);
	$text = str_replace("[i]", "<em>", $text);
	$text = str_replace("[/i]", "</em>", $text);
	//$text = htmlspecialchars($text);
	$text = nl2br($text);

	//Prepare for preg
	$text = str_replace(">", " > ", $text);
	$text = str_replace("<", " <", $text);

	$text = str_replace("\n", "\n ", $text);

	//Wrap emailaddress in <a mailto:>
	$text = preg_replace("/\w+[\w-.]*\w+@\w[\w-.]+\.\w{2,4}/", "<a href=\"mailto:\\0\">\\0</a>", $text);

	//Wrap urls
	//$text = preg_replace("/(\s|^)\w+\.[\w.?&=\/-]+[a-z]{2,4}/", "http://\\0", $text);
	$text = preg_replace("/(\s|^)\w+[\w.]*\.[a-z]{2,4}[a-z.?&=\/-]*/", "http://\\0", $text);
	$text = str_replace("http:// ", " http://", $text);

	//Wrap http:// or https:// in <a>-tags 
	$text = preg_replace("/(((http:)||(https:))\/\/\S+\w)/", "<a href=\"\\0\">\\0</a>", $text);

	return $text;
}

function printPassiveDay($day) {
	print ("<td class=\"passive\"><h4>$day</h4></td>");
}

function printOpenActiveDay($day) {
	print ("<td class=\"active\"><h4>$day</h4>");
}
function printCloseActiveDay() {
	print ("</td>");
}

function displayLogin() {
?>
	<div class="text-column">
<?php


	$title = "Logg inn";
	$enctype = NULL;
	$method = "post";
	$action = "index.php?action=log-in";
    if( isset($_GET['page']) ) {
        $action .= "&page=" . $_GET['page'];
    }
    if( isset($_GET['userid']) ) {
        $action .= "&userid=" . $_GET['userid'];
    }
	$fields = Array ();

	$fields[] = Array (
		"label" => "Epost eller brukernavn",
		"type" => "text",
		"attributes" => Array (
			"name" => "username",
			"size" => 25,
			"maxlength" => 100
		)
	);

	$fields[] = Array (
		"label" => "Passord",
		"type" => "password",
		"attributes" => Array (
			"name" => "password",
			"size" => 12,
			"maxlength" => 50
		)
	);

	$form = new Form($title, $enctype, $method, $action, $fields);
	$form->display("table");
?>
<p>
<a href="https://brukerinfo.neuf.no/accounts/password/reset">Glemt passord eller brukernavn?</a>
</p>
<h3>Ny bruker?</h3>
<p>Hvis du aldri har vært medlem før, kan du <a href="index.php?page=register-user">registrere deg</a>.</p>
  
<h3>Har du spørsmål om medlemskapet ditt?</h3>
<p>
    Send epost til <a href="mailto:medlemskap@studentersamfundet.no">medlemskap@studentersamfundet.no</a>. Oppgi medlemskortnummer og en kort beskrivelse av hva som ikke virker, så skal vi hjelpe deg så fort vi kan.
</p>
<p>
    Andre spørsmål? Kontakt oss på <a href="mailto:support@studentersamfundet.no">support@studentersamfundet.no</a>.
</p>
	</div>
<?php

}

function getNextId($table) {
	$conn = db_connect();
	if (dirname($_SERVER['PHP_SELF']) == "/dns/intranett") {
		$sql = "SELECT auto_increment FROM INFORMATION_SCHEMA.TABLES
				                    WHERE TABLE_SCHEMA = 'dns_intranett' AND TABLE_NAME = '$table'";
		$result = & $conn->query($sql);
		if (DB :: isError($result) != true) {
			if ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
				return $row[0];
			}
		}
	}
	else {
		$sql = "SHOW TABLE STATUS LIKE '$table'";
		$result = & $conn->query($sql);
		if (DB :: isError($result) != true) {
			if ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
				return $row->Auto_increment;
			}
		}
	}
}

function get_file_basename($filename) {
	$pos = strrpos($filename, ".");
	if ($pos === false) {
		return $filename;
	}
	else {
		return substr($filename, 0, $pos);
	}
}

function get_file_suffix($filename) {
	$pos = strrpos($filename, ".");
	if ($pos === false) {
		return "";
	}
	else {
		return substr($filename, $pos);
	}
}

function get_repeat_date($date, $offset, $frequency) {
	switch ($frequency) {

		case 'daily' :
			$newDate = date("Y-m-d H:i", strtotime("+$offset day", strtotime($date)));
			return $newDate;

		case 'weekly' :
			$newDate = date("Y-m-d H:i", strtotime("+$offset week", strtotime($date)));
			return $newDate;

		case 'biweekly' :
			$offset = $offset * 2;
			$newDate = date("Y-m-d H:i", strtotime("+$offset week", strtotime($date)));
			return $newDate;

		case 'monthlyDate' :
			$newDate = date("Y-m-d H:i", strtotime("+$offset month", strtotime($date)));
			return $newDate;

		case 'annual' :
			$newDate = date("Y-m-d H:i", strtotime("+$offset year", strtotime($date)));
			return $newDate;

	}
}

function getUnreadCount($type, $dateField, $userId = NULL) {
	$db_prefix = 'din_';
	$table = $db_prefix . $type;
	if ($userId == NULL) {
		$userId = getCurrentUser();
	}

	$conn = db_connect();

	$sql = sprintf("SELECT COUNT(*) FROM %s t " . "LEFT JOIN %s tr " . "ON t.id =  tr.%s " . "AND tr.user_id = %s " . "WHERE t.%s > NOW() " . "AND tr.user_id IS NULL", $table, $table . "read", $type . "_id", $userId, $dateField);

	$result = & $conn->query($sql);

	if (DB :: isError($result) == true) {
		error("Get count: " . $result->toString());
		return false;
	}
	else {
		if ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
			return $row[0];
		}
		else {
			error('Get count: No result returned');
			return false;
		}
	}
}

function getBugreportCount() {
	$conn = db_connect();
	$sql = "SELECT COUNT(*) FROM din_bugreport WHERE active = 1";
	$result = & $conn->query($sql);

	if (DB :: isError($result) == true) {
		error("Get bugreportcount: " . $result->toString());
		return false;
	}
	else {
		if ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
			return $row[0];
		}
		else {
			error('Get bugreportcount: No result returned');
			return false;
		}
	}
}

function reportBug($type) {
	$GLOBALS['extraScriptParams']['report-bug'] = $type;
}

function displayBugReportForm($type) {
?>
  <span class="btn btn-default" onclick="toggleDisplay('bug-report-form'); toggleText(this, 'rapportér problem', 'skjul skjema');">rapportér problem</span>
  <div id="bug-report-form" style="display: none;">
  <?php


	$title = "rapportér problem";
	$enctype = NULL;
	$method = "post";
	$action = "index.php?action=register-bugreport";
	$fields = Array ();

	$fields[] = Array (
		"label" => "type",
		"type" => "hidden",
		"attributes" => Array (
			"name" => "type",
			"value" => $type
		)
	);
	$fields[] = Array (
		"label" => "filename",
		"type" => "hidden",
		"attributes" => Array (
			"name" => "filename",
			"value" => $_SERVER['PHP_SELF']
		)
	);
	$fields[] = Array (
		"label" => "query",
		"type" => "hidden",
		"attributes" => Array (
			"name" => "query",
			"value" => $_SERVER['QUERY_STRING']
		)
	);
	$fields[] = Array (
		"label" => "referer",
		"type" => "hidden",
		"attributes" => Array (
			"name" => "referer",
			"value" => (isset ($_SERVER['HTTP_REFERER']
		) ? $_SERVER['HTTP_REFERER'] : ""
	)));
	$fields[] = Array (
		"label" => "useragent",
		"type" => "hidden",
		"attributes" => Array (
			"name" => "useragent",
			"value" => $_SERVER['HTTP_USER_AGENT']
		)
	);
	$fields[] = Array (
		"label" => "userid",
		"type" => "hidden",
		"attributes" => Array (
			"name" => "user_id",
		"value" => getCurrentUser()
	));
	$fields[] = Array (
		"label" => "problem",
		"type" => "text",
		"attributes" => Array (
			"name" => "title",
			"size" => 50,
			"maxlength" => 50
		)
	);
	$fields[] = Array (
		"label" => "kommentar",
		"type" => "textarea",
		"attributes" => Array (
			"name" => "comment",
			"cols" => 70,
			"rows" => 5
		)
	);

	$form = new Form($title, $enctype, $method, $action, $fields);
	$form->display();
?></div><?php


}

function import_forening() {
	$conn = db_connect();
	$sql = "SELECT id FROM din_forening";

	$result = & $conn->query($sql);

	if (DB :: isError($result) == true) {
		error("Get id: " . $result->toString());
	}
	else {
		while ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
			$id = $row[0];
			$sql = "INSERT INTO din_division 
						                          SELECT id, navn, tekst, telefon, epost, '000', 5000, link, 0
						                          FROM din_forening
						                          WHERE id = $id";
			$res = $conn->query($sql);
			if (DB :: isError($res) == true) {
				print ("<p><code><pre>Get id: " . $res->toString() . "</pre></code></p>");
			}
			else {
				print ("<p>Division $id inserted</p>");
			}
		}
	}
}

function import_medlemmer_from_csv() {
	$f = file("alle.csv");
	$conn = db_connect();

	foreach ($f as $c) {
		$c = explode(";", $c);
		$email = ($c[0] == "") ? NULL : $c[0];
		$firstname = trim(substr($c[1], 0, strrpos($c[1], " ")));
		$lastname = trim(substr($c[1], strrpos($c[1], " ")));
		$address = $c[2];
		$zipcode = sprintf("%04d", $c[3]);
		$phone = $c[4];
		$birthdate = rtrim($c[5]);
		if ($birthdate != "0000-00-00") {
			$birthdate = explode(".", $birthdate);
			$birthdate = "$birthdate[2]-$birthdate[1]-$birthdate[0]";
		}
		$id = getNextId("din_user");

		$sql = sprintf("INSERT INTO din_user VALUES " . "(%s, NULL, %s, PASSWORD('hemmelig'), %s, %s, %s, %s, %s, %s, 0, %s, NULL, 0)", $conn->quoteSmart($id), $conn->quoteSmart($id), $conn->quoteSmart($firstname), $conn->quoteSmart($lastname), $conn->quoteSmart("no"), $conn->quoteSmart($email), $conn->quoteSmart($birthdate), $conn->quoteSmart(22), $conn->quoteSmart("0000-00-00"));
		$result = $conn->query($sql);
		if (DB :: isError($result) == true) {
			print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
		}

		$sql = "INSERT INTO din_useraddressno VALUES " . "  ($id, '$address', '$zipcode')";
		$result = $conn->query($sql);
		if (DB :: isError($result) == true) {
			print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
		}

		$sql = "INSERT INTO din_usergrouprelationship VALUES (
				                      $id, 1)";
		$result = $conn->query($sql);
		if (DB :: isError($result) == true) {
			print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
		}

		if ($phone != "") {
			$sql = "INSERT INTO din_userphonenumber VALUES " . "  ($id, $phone)";
			$result = $conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}
		}

		$sql = "INSERT INTO din_userupdate VALUES " . "  (NULL, NOW(), $id, 'user imported from csv-file', 1)";
		$result = $conn->query($sql);
		if (DB :: isError($result) == true) {
			print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
		}

		print ("<p>user $id - $firstname $lastname inserted</p>");
	}
}

function import_medlemmer() {
	$conn = db_connect();
	$sql = "SELECT id FROM medlemmer WHERE id >= 5";

	$result = & $conn->query($sql);

	if (DB :: isError($result) == true) {
		print ("Get id: " . $result->toString());
	}
	else {
		while ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
			$user_id = getNextId("din_user");
			$id = $row[0];
			$sql = "INSERT INTO din_user 
						                          SELECT $user_id, id, id, PASSWORD('hemmelig'), fornavn, etternavn, 
						                          'no', epost, fødselsdato, studiested, 1, '0000-00-00', NULL 
						                          FROM medlemmer
						                          WHERE id = $id";
			$conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}

			$sql = "INSERT INTO din_useraddressno
						                          SELECT $user_id, adresse, postnummer
						                          FROM medlemmer
						                          WHERE id = $id";
			$conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}

			$sql = "INSERT INTO din_usergrouprelationship VALUES (
						                          $user_id, 1)";
			$conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}

			$sql = "INSERT INTO din_userphonenumber
						                          SELECT $user_id, telefon
						                          FROM medlemmer
						                          WHERE id = $id";
			$conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}

			$sql = "INSERT INTO din_userupdate (updateDate, updatedUserId, updatedByIP, updateComment, updatedByUserId)
						                          SELECT NOW(), $user_id, 'localhost', 'user imported from dns_medlemmer', 1
						                          FROM medlemmer
						                          WHERE id = $id";
			$conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}

			print ("<p>user $id inserted</p>");
		}
	}
}

function moveLinks() {
	$conn = db_connect();

	$sql = "SELECT * FROM program_linker";

	$result = & $conn->query($sql);

	if (DB :: isError($result) == true) {
		print ("Move links: " . $result->toString());
	}
	else {
		while ($row = & $result->fetchRow(DB_FETCHMODE_OBJECT)) {
			$concert_id = $row->parent_id;
			$url = $row->url;
			$sql = "UPDATE program SET " . "  linker = CONCAT(linker, '\n$url') " . "WHERE " . "  id = $concert_id";
			print $sql;
			$update = $conn->query($sql);
			if (DB :: isError($update) == true) {
				print ("<p><code><pre>Get id: " . $update->toString() . "</pre></code></p>");
			}
		}
	}
}

function subscribe_mailinglist($user_id, $group_id) {
	$group = new Group($group_id);
	if ($group->mailinglist != "") {
		$user = new User($user_id);
		$sendto = $group->mailinglist . "-subscribe@studentersamfundet.no";
		$subject = "subscribe";
		$message = "subscribe";
		$headers = "From: $user->email \r\n";
		if (!@ mail($sendto, $subject, $message, $headers)) {
			error("Ikke kontakt med server for avmelding fra epostliste.");
		}
	}
}

function unsubscribe_mailinglist($user_id, $group_id) {
	$group = new Group($group_id);
	if ($group->mailinglist != "") {
		$user = new User($user_id);
		$sendto = $group->mailinglist . "-unsubscribe@studentersamfundet.no";
		$subject = "unsubscribe";
		$message = "unsubscribe";
		$headers = "From: $user->email \r\n";
		if (!@ mail($sendto, $subject, $message, $headers)) {
			error("Ikke kontakt med server for avmelding fra epostliste.");
		}
	}
}

function registerCardNos() {
	$conn = db_connect();
	$sql = "SELECT cardno FROM din_user WHERE cardno IS NOT NULL";

	$result = & $conn->query($sql);

	if (DB :: isError($result) == true) {
		print ("Regsiter cardnos: " . $result->toString());
	}
	else {
		while ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
			$cardno = $row[0];
			$sql = "INSERT INTO din_usedcardno " . "VALUES ($cardno, NOW())";
			$conn->query($sql);
			if (DB :: isError($result) == true) {
				print ("<p><code><pre>Get id: " . $result->toString() . "</pre></code></p>");
			}
		}
	}
}

function send_welcome_mail($user) {
	$sendto = $user->email;
	$subject = "Velkommen som registrert bruker på Studentersamfundet Inside!";
	$message = "" .

	"Hei, $user->firstname $user->lastname!\n " .
	"\n" .
	"\nDu har nettopp registrert deg som bruker på Studentersamfundet Inside." .
	"\n" .
	"\nDitt brukernavn er $user->username. Om du glemmer passordet ditt kan du få tilsendt nytt ved å besøke lenken under og taste inn din epostadresse. Her kan du også oppdatere din kontaktinformasjon og kjøpe/fornye medlemskap i Det Norske Studentersamfund" .
	"\n" .
	"OBS: Hvis du har kjøpt medlemskap må du gå inn å aktivere det." .
	"\n" .
	"\nhttps://inside.studentersamfundet.no/" .
	"\n" .
	"\nmvh" .
	"\n" .
	"\nStyret i Det Norske Studentersamfund";
	$headers = 'From: medlemskap@studentersamfundet.no' . "\r\n";
	@ mail($sendto, $subject, $message, $headers);
}

function getEnumValues($like, $tabell, $host = "default") {
	$conn = db_connect($host);
	$sql = "SHOW COLUMNS FROM $tabell LIKE '$like'";
	$result = & $conn->query($sql);
	if (DB :: isError($result) == true) {
		error("Get enum-values: " . $result->toString());
		return false;
	}
	else {
		if ($row = & $result->fetchRow(DB_FETCHMODE_ORDERED)) {
			$options = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/", "\\2", $row[1]));
			sort($options);
			foreach ($options as $cur) {
				$list[] = Array (
					"id" => $cur,
					"title" => $cur
				);
			}
			return $list;
		}
		else {
			error('Get enum-values: No result returned');
			return false;
		}
	}
}

function strip_nl($string) {
	$string = str_replace("\n", "", $string);
	$string = str_replace("\r", "", $string);
	return $string;
}


function getStickerPeriod($time)
{
	
	$time = strtotime(getExpiryDate($time));// is_numeric($time)?$time:strtotime($time);
	
	if($time < strtotime('2011-08-01'))
		return date('Y', $time); //gammelt klistremerke
	
	return date('Y-m-d', $time);
}

function getNextMembershipExpiryDate()
{
	return getExpiryDate();
}

/*
 * Returns the expiry date for a given time.
 */
function getExpiryDate($time = null) {
    if($time == null) {
        $time = time();
    }
    $time = is_numeric($time) ? $time : strtotime($time);

    return date("Y-m-d", strtotime("+1 year", $time));
}

// Migration (nikolark)

/* Returns true if the current logged in user have the migrated flag. */
function is_migrated($uid) {
    $conn = db_connect();

    $sql = sprintf("SELECT migrated FROM din_user WHERE id=%s AND migrated IS NOT NULL", $uid);
    $result = $conn->query($sql);

    if (DB :: isError($result) == true) {
        error("is_migrated: " . $result->toString());
        return false;
    }
    return $result->numRows() > 0;
}
/* Update the migration status */
function set_migrated($uid) {
    $conn = db_connect();

    $sql = sprintf("UPDATE din_user SET migrated=NOW() WHERE id=%s", $uid);
    $result = $conn->query($sql);

    if (DB :: isError($result)) {
        return false;
    }
    return true;
}
/* User has valid membership? */
function membership_expired($uid) {
    $conn = db_connect();

    $sql = sprintf("SELECT expires FROM din_user WHERE id=%s AND expires <= NOW()", $uid);
    $result = $conn->query($sql);

    if (DB :: isError($result) == true) {
        error("membership_expired: " . $result->toString());
        return false;
    }
    return $result->numRows() > 0;
}
/* Find a users groups */
function find_groups($uid) {
    $conn = db_connect();
    $sql = "SELECT g.posix_group
        FROM din_usergrouprelationship ugr, din_user u, din_group g
        WHERE ugr.user_id = $uid
        AND ugr.user_id = u.id
        AND ugr.group_id = g.id";
    $conn->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $conn->getAll($sql);

    if(DB :: isError($result)) {
        return false;
    }
    $arr = array();
    foreach($result as $column => $group) {
        $arr[] = $group['posix_group'];
    }

    return $arr;
}
function site_root() {
    $url = 'http://inside.studentersamfundet.no/';
    return $url;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}
function validate_username_length($username) {
    return strlen(stripcslashes($username)) >= 3 && strlen(stripcslashes($username)) <= 12;
}
function validate_username_chars($username) {
    return preg_match('/^[a-z][a-z0-9]*$/', strtolower(stripcslashes($username)));
}
function validate_password_length($password) {
    return strlen($password) >= 8;
}
function validate_password_chars($password) {
    /* No double nor single quotes */
    if( preg_match('/[\\\"\']+/', $password) ) {
        return false;
    }
    return true;
}
function clean_phonenumber($pn) {
    $pn = preg_replace('/[^0-9\+]/', '', $pn); // remove everything except valid chars
    $pn = preg_replace('/^00/','+', $pn); // replace starting 00 with +
    // norwegian phone numbers
    if( strlen($pn) === 8 && ($pn[0] === "4" || $pn[0] === "9") ) {
        $pn = "+47".$pn;
    }
    return $pn;
}
// ISO-8601 Y-m-d
function clean_date($date) {
    // returns a DateTime object
    return date_create_from_format('Y-m-d', $date);
}
// MySQL TIMESTAMP Y-m-d H:i:s
function clean_timestamp($date) {
    if($date instanceof DateTime) {
        return $date;  // already cleaned, bail
    }
    // returns a DateTime object
    return date_create_from_format('Y-m-d H:i:s', $date);
}
// E.164
function valid_phonenumber($phone) {
    return preg_match('/^\+?\d{8,15}$/i', $phone);
}
function valid_email($email) {
    if( substr($email, -4) === ".con" ) { // .con is a common typo and not a TLD atm.
        return false;
    }
    if( substr($email, -8) === "gmail.co" ) { // gmail.co is a common typo
        return false;
    }
    $parts = explode("@", $email);
    // domain part does not need a . in some PHP-versions, fix that
    if( count($parts) == 2 && strstr($parts[1], ".") === false) {
        return false;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/* Set HTTP response code */
function set_response_code($code) {
    if(!is_int($code)) {
        return false;
    }
    header('X-Ignore-This: something', true, $code);
}
/* Redirect to path on same domain */
function redirect($path) {
    $scheme ="http".((empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off')?'':'s');
    $host = $_SERVER['HTTP_HOST'];
    $location = "$scheme://$host$path";
    header("Location: $location");
}

function log_userupdate($updated, $comment, $updated_by=NULL) {
    $conn = db_connect();

    if( $updated_by == NULL) {
        $updated_by = $updated;
    }

    $now = date_format(date_create(), "Y-m-d H:i:s");
    $data = array(
        'date' => $now,
        'user_id_updated' => $updated,
        'comment' => $comment,
        'user_id_updated_by' => $updated_by
    );
    $res = $conn->autoExecute("din_userupdate", $data, DB_AUTOQUERY_INSERT);
    
    if (DB::isError($res)) {
        var_dump($res->toString());

        return false;
    }
    return true;
}

function hash_ldap_password($raw_password) {
	$salt = openssl_random_pseudo_bytes(4);

	return '{SSHA}' . base64_encode(sha1( $raw_password.$salt, TRUE ). $salt);
}

?>
